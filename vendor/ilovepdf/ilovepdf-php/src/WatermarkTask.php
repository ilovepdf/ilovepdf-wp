<?php

namespace Ilovepdf;

use Ilovepdf\Element;

/**
 * Class WatermarkTask
 *
 * @package Ilovepdf
 */
class WatermarkTask extends Task
{
    /**
     * @var string|null
     */
    public $mode;

    /**
     * @var string[]
     */
    private $modeValues = ['image', 'text', 'multi'];

    /**
     * @var string|null
     */
    public $text;

    /**
     * @var string|null
     */
    public $image;

    /**
     * @var string|null
     */
    public $pages;

    /**
     * @var string|null
     */
    public $vertical_position;

    /**
     * @var string[]
     */
    private $verticalPositionValues = ['bottom', 'middle', 'top'];
    /**
     * @var string|null
     */
    public $horizontal_position;

    /**
     * @var string[]
     */
    private $horizontalPositionValues = ['left', 'center', 'right'];

    /**
     * @var integer|null
     */
    public $vertical_position_adjustment;

    /**
     * @var integer|null
     */
    public  $horizontal_position_adjustment;

    /**
     * @var boolean|null
     */
    public $mosaic;

    /**
     * @var integer|null
     */
    public $rotation;

    /**
     * @var string|null
     */
    public $font_family;

    /**
     * @var string[]
     */
    private $fontFamilyValues = ['Arial', 'Arial Unicode MS', 'Verdana', 'Courier', 'Times New Roman', 'Comic Sans MS', 'WenQuanYi Zen Hei', 'Lohit Marathi'];

    /**
     * @var string|null
     */
    public $font_style;

    /**
     * @var integer|null
     */
    public $font_size;

    /**
     * @var string|null
     */
    public $font_color;

    /**
     * @var integer|null
     */
    public $transparency;

    /**
     * @var string|null
     */
    public $layer;

    /**
     * @var string[]
     */
    private $layerValues = ['above', 'below'];

    /**
     * @var array
     */
    public $elements = [];


    /**
     * WatermarkTask constructor.
     *
     * @param null|string $publicKey    Your public key
     * @param null|string $secretKey    Your secret key
     * @param bool $makeStart           Set to false for chained tasks, because we don't need the start
     */
    function __construct(?string $publicKey, ?string $secretKey, $makeStart = true)
    {
        $this->tool='watermark';
        parent::__construct($publicKey, $secretKey, $makeStart);
    }



    /**
     * @param string $mode
     */
    public function setMode(string $mode): self
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @param string $image
     */
    public function setImage(string $image): self
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @param File $image
     */
    public function setImageFile(File $imageFile): self
    {
        $this->image = $imageFile->getServerFilename();
        return $this;
    }

    /**
     * @param string $pages
     */
    public function setPages(string $pages): self
    {
        $this->pages = $pages;
        return $this;
    }

    /**
     * @param string $vertical_position
     */
    public function setVerticalPosition(string $vertical_position): self
    {
        $this->checkValues($vertical_position, $this->verticalPositionValues);

        $this->vertical_position = $vertical_position;
        return $this;
    }

    /**
     * @param string $horizontal_position
     */
    public function setHorizontalPosition(string $horizontal_position): self
    {
        $this->checkValues($horizontal_position, $this->horizontalPositionValues);

        $this->horizontal_position = $horizontal_position;
        return $this;
    }

    /**
     * @param int $vertical_position_adjustment
     */
    public function setVerticalPositionAdjustment(int $vertical_position_adjustment): self
    {
        $this->vertical_position_adjustment = $vertical_position_adjustment;
        return $this;
    }

    /**
     * @param int $horizontal_position_adjustment
     */
    public function setHorizontalPositionAdjustment(int $horizontal_position_adjustment): self
    {
        $this->horizontal_position_adjustment = $horizontal_position_adjustment;
        return $this;
    }

    /**
     * @param bool $mosaic
     */
    public function setMosaic(bool $mosaic): self
    {
        $this->mosaic = $mosaic;
        return $this;
    }

    /**
     * @param int $rotation
     */
    public function setRotation(int $rotation): self
    {
        $this->rotation = $rotation;
        return $this;
    }

    /**
     * @param string $font_family
     */
    public function setFontFamily(string $font_family): self
    {
        $this->checkValues($font_family, $this->fontFamilyValues);

        $this->font_family = $font_family;
        return $this;
    }

    /**
     * @param string $font_style
     */
    public function setFontStyle(string $font_style): self
    {
        $this->font_style = $font_style;
        return $this;
    }

    /**
     * @param int $font_size
     */
    public function setFontSize($font_size): self
    {
        $this->font_size = $font_size;
        return $this;
    }

    /**
     * @param string $font_color
     */
    public function setFontColor(string $font_color): self
    {
        $this->font_color = $font_color;
        return $this;
    }

    /**
     * @param int $transparency
     */
    public function setTransparency(int $transparency): self
    {
        $this->transparency = $transparency;
        return $this;
    }

    /**
     * @param string $layer
     */
    public function setLayer(string $layer): self
    {
        $this->checkValues($layer, $this->layerValues);

        $this->layer = $layer;
        return $this;
    }

    /**
     * adds a watermark element
     *
     * @param Element|array $element
     * @return $this
     */
    public function addElement($element): self
    {

        if (is_a($element, Element::class)) {
            $this->elements[] = $element;
        } elseif (is_array($element)) {
            $this->elements[] = new Element($element);
        }
        return $this;
    }
}
