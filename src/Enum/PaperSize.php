<?php

namespace App\Enum;

enum PaperSize: string
{
    case A0_PORTRAIT = 'A0_portrait';
    case A0_LANDSCAPE = 'A0_landscape';
    case A1_PORTRAIT = 'A1_portrait';
    case A1_LANDSCAPE = 'A1_landscape';
    case A2_PORTRAIT = 'A2_portrait';
    case A2_LANDSCAPE = 'A2_landscape';
    case A3_PORTRAIT = 'A3_portrait';
    case A3_LANDSCAPE = 'A3_landscape';
    case A4_PORTRAIT = 'A4_portrait';
    case A4_LANDSCAPE = 'A4_landscape';
    case A5_PORTRAIT = 'A5_portrait';
    case A5_LANDSCAPE = 'A5_landscape';
    case A6_PORTRAIT = 'A6_portrait';
    case A6_LANDSCAPE = 'A6_landscape';
    case A7_PORTRAIT = 'A7_portrait';
    case A7_LANDSCAPE = 'A7_landscape';
    case A8_PORTRAIT = 'A8_portrait';
    case A8_LANDSCAPE = 'A8_landscape';
    case A9_PORTRAIT = 'A9_portrait';
    case A9_LANDSCAPE = 'A9_landscape';
    case A10_PORTRAIT = 'A10_portrait';
    case A10_LANDSCAPE = 'A10_landscape';

    case B0_PORTRAIT = 'B0_portrait';
    case B0_LANDSCAPE = 'B0_landscape';
    case B1_PORTRAIT = 'B1_portrait';
    case B1_LANDSCAPE = 'B1_landscape';
    case B2_PORTRAIT = 'B2_portrait';
    case B2_LANDSCAPE = 'B2_landscape';
    case B3_PORTRAIT = 'B3_portrait';
    case B3_LANDSCAPE = 'B3_landscape';
    case B4_PORTRAIT = 'B4_portrait';
    case B4_LANDSCAPE = 'B4_landscape';
    case B5_PORTRAIT = 'B5_portrait';
    case B5_LANDSCAPE = 'B5_landscape';
    case B6_PORTRAIT = 'B6_portrait';
    case B6_LANDSCAPE = 'B6_landscape';
    case B7_PORTRAIT = 'B7_portrait';
    case B7_LANDSCAPE = 'B7_landscape';
    case B8_PORTRAIT = 'B8_portrait';
    case B8_LANDSCAPE = 'B8_landscape';
    case B9_PORTRAIT = 'B9_portrait';
    case B9_LANDSCAPE = 'B9_landscape';
    case B10_PORTRAIT = 'B10_portrait';
    case B10_LANDSCAPE = 'B10_landscape';

    case C0_PORTRAIT = 'C0_portrait';
    case C0_LANDSCAPE = 'C0_landscape';
    case C1_PORTRAIT = 'C1_portrait';
    case C1_LANDSCAPE = 'C1_landscape';
    case C2_PORTRAIT = 'C2_portrait';
    case C2_LANDSCAPE = 'C2_landscape';
    case C3_PORTRAIT = 'C3_portrait';
    case C3_LANDSCAPE = 'C3_landscape';
    case C4_PORTRAIT = 'C4_portrait';
    case C4_LANDSCAPE = 'C4_landscape';
    case C5_PORTRAIT = 'C5_portrait';
    case C5_LANDSCAPE = 'C5_landscape';
    case C6_PORTRAIT = 'C6_portrait';
    case C6_LANDSCAPE = 'C6_landscape';
    case C7_PORTRAIT = 'C7_portrait';
    case C7_LANDSCAPE = 'C7_landscape';
    case C8_PORTRAIT = 'C8_portrait';
    case C8_LANDSCAPE = 'C8_landscape';
    case C9_PORTRAIT = 'C9_portrait';
    case C9_LANDSCAPE = 'C9_landscape';
    case C10_PORTRAIT = 'C10_portrait';
    case C10_LANDSCAPE = 'C10_landscape';

    case LETTER_PORTRAIT = 'letter_portrait';
    case LETTER_LANDSCAPE = 'letter_landscape';
    case LEGAL_PORTRAIT = 'legal_portrait';
    case LEGAL_LANDSCAPE = 'legal_landscape';

    public function size(): string
    {
        return explode('_', $this->value, 2)[0];
    }

    public function orientation(): string
    {
        return explode('_', $this->value, 2)[1];
    }
}
