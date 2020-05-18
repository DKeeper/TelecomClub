<?php

namespace components;

/**
 * Class LoremIpsum
 */
class LoremIpsum
{
    private $url = 'https://loripsum.net/api/';

    /**
     * @var bool
     */
    private $code = false;

    /**
     * @var bool
     */
    private $headers = false;

    /**
     * @var string
     */
    private $length = 'short';

    /**
     * @var int
     */
    private $numberOfParagraph = 1;

    /**
     * @return array|string[]
     */
    public function availableLengthOfParagraph(): array
    {
        return ['short', 'medium', 'long', 'verylong'];
    }

    /**
     * @param bool $code
     *
     * @return LoremIpsum
     */
    public function setCode(bool $code): LoremIpsum
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @param bool $headers
     *
     * @return LoremIpsum
     */
    public function setHeaders(bool $headers): LoremIpsum
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @param string $length
     *
     * @return LoremIpsum
     */
    public function setLength(string $length): LoremIpsum
    {
        if (in_array($length, $this->availableLengthOfParagraph(), true)) {
            $this->length = $length;
        }

        return $this;
    }

    /**
     * @param int $numberOfParagraph
     *
     * @return LoremIpsum
     */
    public function setNumberOfParagraph(int $numberOfParagraph): LoremIpsum
    {
        $this->numberOfParagraph = $numberOfParagraph;

        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        $url = $this->url
            . $this->numberOfParagraph
        ;

        if ($this->length) {
            $url .= '/' . $this->length;
        }

        if ($this->code) {
            $url .= '/code';
        }

        if ($this->headers) {
            $url .= '/headers';
        }

        return (string) file_get_contents($url);
    }
}
