<?php

namespace Jikan\Get;

use Jikan\Lib\Parser\CharacterParse;
use Jikan\Lib\Parser\CharacterPicturesParse;

class Character extends Get
{

    public $canonical_path;

    private $validExtends = [PICTURES];

    public function __construct($id = null, $extend = null) {


        if (is_null($id)) {
            throw new \Exception('No ID/Path Given');
        }

        $this->id = $id;

        $this->parser = new CharacterParse;
        $this->parser->setPath(
            (
                is_int($this->id) || ctype_digit($this->id)    
            ) ? BASE_URL . CHARACTER_ENDPOINT . $this->id : $this->id
        );
        $this->parser->loadFile();

        $this->response['code'] = $this->parser->status;
        $this->response = array_merge($this->response, $this->parser->parse());

        $this->canonical_path = $this->parser->model->get('Character', 'link_canonical');

        if (!empty($extend)) {
            $this->extend = $extend;

            foreach ($this->extend as $key => $extend) {

                if (is_string($key)) {
                    $this->extend = $key;
                    $this->extendArgs = $extend;

                    if (!in_array($this->extend, $this->validExtends)) {
                        throw new \Exception('Unsupported parse request');
                    }

                    $this->{$this->extend}($this->extendArgs);
                } elseif (is_int($key)) {
                    $this->extend = $extend;

                    if (!in_array($this->extend, $this->validExtends)) {
                        throw new \Exception('Unsupported parse request');
                    }

                    $this->{$this->extend}();
                }
            }
        }

    }

    private function pictures() {
        $this->parser = new CharacterPicturesParse;

        $this->parser->setPath($this->canonical_path.'/pictures');
        $this->parser->loadFile();

        $this->response = array_merge($this->response, $this->parser->parse());
    }

}