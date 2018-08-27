<?php

class ByteText
{
    /**
     * @var array
     */
    protected static $signs = [
        'B',
        'kB',
        'MB',
        'GB',
        'TB',
        'PB',
        'YB',
        'ZB',
    ];

    /** @var mixed */
    protected $bytes = 0.0;

    /**
     * @param int|float $value
     *
     * @return string
     */
    protected static function factor($value)
    {
        return $value * 1024;
    }

    /**
     * @param float $bytes
     *
     * @return ByteText
     */
    public static function fromBytes($bytes)
    {
        return new self($bytes);
    }

    /**
     * @param float $kiloBytes
     *
     * @return ByteText
     */
    public static function fromKiloBytes($kiloBytes)
    {
        return self::fromBytes(self::factor($kiloBytes));
    }

    /**
     * @param float $megaBytes
     *
     * @return ByteText
     */
    public static function fromMegaBytes($megaBytes)
    {
        return self::fromKiloBytes(self::factor($megaBytes));
    }

    /**
     * @param mixed $bytes
     */
    public function __construct($bytes)
    {
        $this->bytes = $bytes;
    }

    /**
     * @param float $value
     * @param int   $precision
     * @param int   $signIndex
     *
     * @return string
     */
    protected function formatNumber($value, $precision, $signIndex)
    {
        return round($value, $precision) . ' ' . self::$signs[$signIndex];
    }

    /**
     * @param int $precision
     *
     * @return string
     */
    public function toString($precision = 2)
    {
        $value = self::factor($this->bytes);

        $max = count(self::$signs);
        for ($i = 0; $i < $max; $i++) {
            $value = $value / 1024;

            if ($value <= 1024) {
                return $this->formatNumber((float)$value, $precision, $i);
            }
        }

        return $this->formatNumber((float)$value, $precision, $max - 1);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->toString();
    }
}
