<?php
namespace DTUXBase\Util;
/**
 * Text utils
 * @category DTUXBase
 * @package DTUXBase
 * @subpackage Controller
 * @author Diego Pereira Grassato <diego.grassato@gmail.com>
 * @data 31/10/13 15:38
 */
class Text
{
    // Text align constants

    const TEXT_ALIGN_LEFT                   = 0x0000;
    const TEXT_ALIGN_RIGHT                  = 0x0001;
    const TEXT_ALIGN_CENTER                 = 0x0002;
    const TEXT_ALIGN_JUSTIFY                = 0x0004;
    const TEXT_ALIGN_FLAG_JUSTIFY_ALL_LINES = 0x0100; // Distribute all lines

    /**
     * Aligns text left, centered, right or justified
     *
     * @param string $text
     * @param integer $align
     * @param integer $width
     * @param string $newline Newline sequence
     * @param boolean $cut
     * @param integer $paragraph_sep_lines
     * @param integer $text_indent
     * @return ?|string
     */
    public static function textAlign($text, $align = self::TEXT_ALIGN_LEFT, $text_width = 76, $newline_str = "\n",
                                     $cut_words = true, $paragraph_sep_lines = 1, $paragraph_indent = 0)
    {
        // remove redundant spaces
        $text = trim(preg_replace('/ +/', ' ', $text));

        switch ($align & 0x0F)
        {
            case self::TEXT_ALIGN_LEFT:
            {
                // add paragraph indents
                if ($paragraph_indent > 0)
                {
                    $indent = str_repeat("\0", $paragraph_indent);
                    $text = $indent . str_replace($newline_str, $newline_str . $indent, $text);
                }

                $text = str_replace($newline_str, str_repeat($newline_str, $paragraph_sep_lines + 1), $text);
                $text = wordwrap($text, $text_width, $newline_str, $cut_words);
                return str_replace("\0", ' ', $text);
            }

            case self::TEXT_ALIGN_CENTER:
            case self::TEXT_ALIGN_RIGHT:
            {
                $text   = str_replace($newline_str, str_repeat($newline_str, $paragraph_sep_lines + 1), $text);
                $text   = wordwrap($text, $text_width, $newline_str, $cut_words);
                $lines  = explode($newline_str, $text);

                $pad_type = $align == self::TEXT_ALIGN_RIGHT ? STR_PAD_LEFT : STR_PAD_BOTH;

                for ($l = 0; $l < count($lines); $l++)
                {
                    $line_len = strlen($lines[$l]);
                    if ($line_len == 0) { continue; }
                    $line_add = $text_width - $line_len;

                    if ($line_add > 0)
                    {
                        $lines[$l] = str_pad($lines[$l], $text_width, ' ', $pad_type);
                    }
                }

                return implode($newline_str, $lines);
            }

            case self::TEXT_ALIGN_JUSTIFY:
            {
                // split text into paragraphs
                $paragraphs = explode($newline_str, $text);

                for ($p = 0; $p < count($paragraphs); $p++)
                {
                    // trim paragraph
                    $paragraphs[$p] = trim($paragraphs[$p]);

                    // add paragraph indents
                    if ($paragraph_indent > 0)
                    {
                        $indent         = str_repeat("\0", $paragraph_indent);
                        $paragraphs[$p] = $indent . str_replace($newline_str, $newline_str . $indent, $paragraphs[$p]);
                        $nulls_added    = true;
                    }

                    // wrap paragraph words
                    $paragraphs[$p] = wordwrap($paragraphs[$p], $text_width, $newline_str, $cut_words);

                    // split paragraph into lines
                    $paragraphs[$p] = explode($newline_str, $paragraphs[$p]);

                    // last line index
                    $pl_to = ($align & self::TEXT_ALIGN_FLAG_JUSTIFY_ALL_LINES) ? count($paragraphs[$p]) : count($paragraphs[$p]) - 1;

                    for ($pl = 0; $pl < $pl_to; $pl++)
                    {
                        // spaces to be added
                        $line_spaces_to_add = $text_width - strlen($paragraphs[$p][$pl]);
                        // split line
                        $paragraphs[$p][$pl] = explode(' ', $paragraphs[$p][$pl]);
                        // number of words per line
                        $line_word_count = count($paragraphs[$p][$pl]);

                        if ($line_word_count > 1 && $line_spaces_to_add > 0)
                        {
                            // spaces per each word (float)
                            $line_spaces_per_word = $line_spaces_to_add / ($line_word_count - 1);
                            $word_spaces_to_add   = 0;

                            for ($w = 0; $w < $line_word_count - 1; $w++)
                            {
                                // (float) spaces to add
                                $word_spaces_to_add += $line_spaces_per_word;
                                // actual number of spaces to add (int)
                                $word_spaces_to_add_int = (int) round($word_spaces_to_add);

                                if ($word_spaces_to_add_int > 0)
                                {
                                    $paragraphs[$p][$pl][$w] .= str_repeat(' ', $word_spaces_to_add_int);
                                    $word_spaces_to_add -= $word_spaces_to_add_int;
                                }
                            }
                        }

                        // restore line
                        $paragraphs[$p][$pl] = implode(' ', $paragraphs[$p][$pl]);
                    }

                    // replace "\0" with spaces
                    if ($nulls_added)
                    {
                        $paragraphs[$p][0] = str_replace("\0", ' ', $paragraphs[$p][0]);
                    }

                    // restore paragraph
                    $paragraphs[$p] = implode($newline_str, $paragraphs[$p]);
                }

                // restore text
                $paragraphs = implode(str_repeat($newline_str, $paragraph_sep_lines + 1), $paragraphs);

                return $paragraphs;
            }
        }
    }

    /**
     * Indent or un-indent text
     *
     * @param string $str
     * @param string $indent_str
     * @param integer $indentation
     * @param string $newline_str
     * @return string
     */
    public static function textIndent($str, $indent_str, $indentation, $newline_str = "\n")
    {
        if ($indentation != 0)
        {
            $lines = explode($newline_str, $str);

            if ($indentation < 0)
            {
                for ($i = 0; $i < count($lines); $i++)
                {
                    // Find leading tabs

                    $remove_indents = 0;

                    for ($ii = 0; $ii < strlen($lines[$i]); $ii += strlen($indent_str))
                    {
                        if (substr($lines[$i], $ii, strlen($indent_str)) != $indent_str)
                        {
                            break;
                        }
                        else if ($remove_indents >= -$indentation)
                        {
                            break;
                        }
                        else
                        {
                            $remove_indents++;
                        }
                    }

                    // Remove leading tabs
                    $lines[$i] = substr($lines[$i], $remove_indents * strlen($indent_str));
                }
            }
            else
            {
                // == repeat(tab) + line
                $indent_str_full = str_repeat($indent_str, max($indentation, 0));

                for ($i = 0; $i < count($lines); $i++)
                {
                    $lines[$i] = $indent_str_full . $lines[$i];
                }
            }

            $str = implode($newline_str, $lines);
        }

        return $str;
    }
}