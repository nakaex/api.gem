<?php

namespace Api\Bases;

use \Api\Database\Database;
use \stdClass;

class Model
{
    protected $dbh;
    protected $param;
    protected $data = [];

    function __construct()
    {
        $this->param = new stdClass();

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                foreach ($_GET as $k => $v) {
                    $this->param->$k = $v;
                }
                break;
            case 'POST':
                foreach ($_POST as $k => $v) {
                    $this->param->$k = $v;
                }
                break;
            default:
                foreach ($this->parseParameter() as $k => $v) {
                    $this->param->$k = $v;
                }
                break;
        }

        $this->dbh = new Database();
    }

    public function response()
    {
        echo json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    private function parseParameter()
    {
        /* PUT data comes in on the stdin stream */
        $data = fopen("php://input", "r");

        /* Open a file for writing */
        $raw_data = '';

        /* Read the data 1 KB at a time and write to the file */
        while ($chunk = fread($data, 1024))
            $raw_data .= $chunk;

        /* Close the streams */
        fclose($data);

        // Fetch content and determine boundary
        $boundary = substr($raw_data, 0, strpos($raw_data, "\r\n"));

        if (empty($boundary)) {
            parse_str($raw_data, $data);
            return;
        }

        // Fetch each part
        $parts = array_slice(explode($boundary, $raw_data), 1);
        $data = array();

        foreach ($parts as $part) {
            // If this is the last part, break
            if ($part == "--\r\n") break;

            // Separate content from headers
            $part = ltrim($part, "\r\n");
            list($raw_headers, $body) = explode("\r\n\r\n", $part, 2);

            // Parse the headers list
            $raw_headers = explode("\r\n", $raw_headers);
            $headers = array();
            foreach ($raw_headers as $header) {
                list($name, $value) = explode(':', $header);
                $headers[strtolower($name)] = ltrim($value, ' ');
            }

            // Parse the Content-Disposition to get the field name, etc.
            if (isset($headers['content-disposition'])) {
                $filename = null;
                $tmp_name = null;
                preg_match(
                    '/^(.+); *name="([^"]+)"(; *filename="([^"]+)")?/',
                    $headers['content-disposition'],
                    $matches
                );
                list(, $type, $name) = $matches;

                //Parse File
                if (isset($matches[4])) {
                    //if labeled the same as previous, skip
                    if (isset($_FILES[$matches[2]])) {
                        continue;
                    }

                    //get filename
                    $filename = $matches[4];

                    //get tmp name
                    $filename_parts = pathinfo($filename);
                    $tmp_name = tempnam(sys_get_temp_dir(), $filename_parts['filename']);

                    file_put_contents($tmp_name, $body);

                    //populate $_FILES with information, size may be off in multibyte situation

                    $_FILES[$matches[2]] = array(
                        'error' => 0,
                        'name' => $filename,
                        'tmp_name' => $tmp_name,
                        'size' => strlen($body),
                        'type' => $value
                    );

                    //place in temporary directory
                    file_put_contents($tmp_name, $body);
                }
                //Parse Field
                else {
                    $data[$name] = substr($body, 0, strlen($body) - 2);
                }
            }
        }
        return $data;
    }
}
