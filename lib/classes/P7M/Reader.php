<?php

class P7M_Reader
{
    private $bin;

    private $p7m;

    private $originalFile;

    private $certFile;

    public function __construct(SplFileObject $p7m)
    {
        $this->bin = trim(shell_exec('command -v openssl'));
        $this->p7m = $p7m;
		
        $originalFile = substr($this->p7m->getPathname(), 0, -4);
        $output = array();
        $return_var = 0;
        echo sprintf(
        		'%s smime -verify -inform DER -in %s -noverify -out %s',
        		$this->bin,
        		escapeshellarg($this->p7m->getPathname()),
        		escapeshellarg($originalFile)
        );
        exec(
            sprintf(
                '%s smime -verify -inform DER -in %s -noverify -out %s',
                $this->bin,
                escapeshellarg($this->p7m->getPathname()),
                escapeshellarg($originalFile)
            ),
            $output,
            $return_var
        );

        if ($return_var !== 0) {
            throw new Exception(implode(PHP_EOL, $output));
        }

        $this->originalFile = new SplFileObject($originalFile);

        $certFile = $this->p7m->getPathname() . '.crt';
        $output = array();
        $return_var = 0;

        exec(
            sprintf(
                '%s pkcs7 -inform DER -print_certs -in %s -out %s',
                $this->bin,
                escapeshellarg($this->p7m->getPathname()),
                escapeshellarg($certFile)
            ),
            $output,
            $return_var
        );

        if ($return_var !== 0) {
            throw new Exception(implode(PHP_EOL, $output));
        }

        $this->certFile = new SplFileObject($certFile);
    }

    public function getP7mFile()
    {
        return $this->p7m;
    }

    public function getOriginalFile()
    {
        return $this->originalFile;
    }

    public function getCertFile()
    {
        return $this->certFile;
    }

    public function getCertData()
    {
        return openssl_x509_parse(file_get_contents($this->certFile->getPathname()));
    }
}
