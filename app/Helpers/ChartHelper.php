<?php

namespace App\Helpers;

class ChartHelper
{
    protected static array $chartColors = [
        [20, 184, 166], // Teal (ALPHA.CORP primary)
        [245, 158, 11], // Amber
        [99, 102, 241], // Indigo
        [239, 68, 68],  // Red
        [139, 92, 246], // Purple
        [236, 72, 153], // Pink
        [6, 182, 212],  // Cyan
        [132, 204, 22], // Lime
        [251, 146, 60], // Orange
        [168, 85, 247], // Violet
    ];

    /**
     * Generate a line chart image for revenue data
     */
    public static function lineChart(array $data, string $title, string $yLabel = ''): string
    {
        $w = 880; $h = 280;
        $image = self::createCanvas($w, $h);
        
        $white = imagecolorallocate($image, 255, 255, 255);
        $dark = imagecolorallocate($image, 15, 23, 42);
        $gray = imagecolorallocate($image, 100, 116, 139);
        $gridColor = imagecolorallocate($image, 226, 232, 240);
        $teal = imagecolorallocate($image, 20, 184, 166);
        $fillColor = imagecolorallocatealpha($image, 20, 184, 166, 50);
        $bgPlot = imagecolorallocate($image, 248, 250, 252);
        
        $lm = 70; $rm = 25; $tm = 35; $bm = 40;
        $pw = $w - $lm - $rm; $ph = $h - $tm - $bm;
        
        // Title
        self::drawTitle($image, $title, $w, $tm, $dark);
        
        // Plot background
        imagefilledrectangle($image, $lm, $tm, $w - $rm, $h - $bm, $bgPlot);
        imagerectangle($image, $lm, $tm, $w - $rm, $h - $bm, $gridColor);
        
        if (empty($data) || max(array_filter($data, 'is_numeric')) == 0) {
            self::drawNoData($image, $w, $h, $gray);
            return self::output($image);
        }

        $values = array_map('floatval', $data);
        $maxVal = max($values);
        $minVal = min($values);
        $range = ($maxVal - $minVal) ?: ($maxVal ?: 1);
        $count = count($values);

        // Y-axis grid
        $gridLines = 4;
        for ($i = 0; $i <= $gridLines; $i++) {
            $y = $tm + $ph - (int)($ph * $i / $gridLines);
            imageline($image, $lm, $y, $w - $rm, $y, $gridColor);
            $val = $minVal + ($range * $i / $gridLines);
            imagestring($image, 2, 3, $y - 5, self::shortNumber($val), $gray);
        }

        // X labels (months)
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        for ($i = 0; $i < $count; $i++) {
            $x = $lm + ($i > 0 ? (int)($pw * $i / ($count - 1)) : $lm);
            if ($count <= 1) break;
            $label = $monthNames[$i] ?? ($i + 1);
            $lx = $x - (int)(strlen((string)$label) * imagefontwidth(2) / 2);
            imagestring($image, 2, max(0, $lx), $h - $bm + 5, (string)$label, $gray);
        }

        // Plot points
        $points = [];
        for ($i = 0; $i < $count; $i++) {
            $x = $lm + ($count > 1 ? (int)($pw * $i / ($count - 1)) : $lm + (int)($pw / 2));
            $y = $tm + $ph - (int)(($values[$i] - $minVal) * $ph / $range);
            $points[] = ['x' => $x, 'y' => $y];
        }

        // Fill area under line
        if ($count > 1) {
            $polyPoints = [$points[0]['x'], $h - $bm];
            foreach ($points as $p) { $polyPoints[] = $p['x']; $polyPoints[] = $p['y']; }
            $polyPoints[] = $points[$count - 1]['x']; $polyPoints[] = $h - $bm;
            imagefilledpolygon($image, $polyPoints, $fillColor);
        }

        // Draw line
        for ($i = 0; $i < $count - 1; $i++) {
            imagesetthickness($image, 2);
            imageline($image, $points[$i]['x'], $points[$i]['y'], $points[$i + 1]['x'], $points[$i + 1]['y'], $teal);
            imagesetthickness($image, 1);
        }

        // Draw points
        foreach ($points as $point) {
            imagefilledellipse($image, $point['x'], $point['y'], 6, 6, $white);
            imagefilledellipse($image, $point['x'], $point['y'], 4, 4, $teal);
        }

        return self::output($image);
    }

    /**
     * Generate a bar chart for monthly events
     */
    public static function barChart(array $data, string $title): string
    {
        $w = 880; $h = 280;
        $image = self::createCanvas($w, $h);
        
        $white = imagecolorallocate($image, 255, 255, 255);
        $dark = imagecolorallocate($image, 15, 23, 42);
        $gray = imagecolorallocate($image, 100, 116, 139);
        $gridColor = imagecolorallocate($image, 226, 232, 240);
        $barColor = imagecolorallocate($image, 20, 184, 166);
        $barDark = imagecolorallocate($image, 13, 148, 136);
        $bgPlot = imagecolorallocate($image, 248, 250, 252);
        
        $lm = 55; $rm = 25; $tm = 35; $bm = 40;
        $pw = $w - $lm - $rm; $ph = $h - $tm - $bm;

        self::drawTitle($image, $title, $w, $tm, $dark);
        imagefilledrectangle($image, $lm, $tm, $w - $rm, $h - $bm, $bgPlot);
        imagerectangle($image, $lm, $tm, $w - $rm, $h - $bm, $gridColor);

        $values = array_map('intval', $data);
        $maxVal = max($values) ?: 1;
        $count = count($values);

        // Y-axis
        $gridLines = 4;
        for ($i = 0; $i <= $gridLines; $i++) {
            $y = $tm + $ph - (int)($ph * $i / $gridLines);
            imageline($image, $lm, $y, $w - $rm, $y, $gridColor);
            $val = (int)($maxVal * $i / $gridLines);
            imagestring($image, 2, 3, $y - 5, (string)$val, $gray);
        }

        $barWidth = max(10, (int)(($pw - 20) / $count * 0.7));
        $gap = (int)(($pw - 20) / $count * 0.3);

        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        for ($i = 0; $i < $count; $i++) {
            $x = $lm + 10 + (int)(($barWidth + $gap) * $i) + (int)($gap / 2);
            $barH = (int)(($values[$i] / $maxVal) * $ph);
            $barY = $tm + $ph - $barH;

            // Draw bar with gradient effect
            imagefilledrectangle($image, $x, $barY, $x + $barWidth, $tm + $ph - 1, $barColor);
            if ($barH > 2) {
                imagefilledrectangle($image, $x, $barY, $x + $barWidth, $barY + min(4, $barH), $barDark);
            }

            // X label
            $label = $monthNames[$i] ?? ($i + 1);
            $lx = $x + (int)($barWidth / 2) - (int)(strlen((string)$label) * imagefontwidth(2) / 2);
            imagestring($image, 2, max(0, $lx), $h - $bm + 5, (string)$label, $gray);

            // Value on top
            $valStr = (string)$values[$i];
            $vx = $x + (int)($barWidth / 2) - (int)(strlen($valStr) * imagefontwidth(2) / 2);
            imagestring($image, 2, max(0, $vx), $barY - 12, $valStr, $dark);
        }

        return self::output($image);
    }

    /**
     * Generate a pie chart for event status distribution
     */
    public static function pieChart(array $data, string $title): string
    {
        $w = 440; $h = 280;
        $image = self::createCanvas($w, $h);
        
        $white = imagecolorallocate($image, 255, 255, 255);
        $dark = imagecolorallocate($image, 15, 23, 42);
        $gray = imagecolorallocate($image, 100, 116, 139);
        
        self::drawTitle($image, $title, $w, 30, $dark);

        if (empty($data) || array_sum($data) == 0) {
            self::drawNoData($image, $w, $h, $gray);
            return self::output($image);
        }

        $total = array_sum($data);
        $cx = 160; $cy = 155; $radius = 100;
        $startAngle = 0;

        $labels = array_keys($data);
        $values = array_values($data);

        // Draw pie segments
        foreach ($values as $idx => $value) {
            if ($value == 0) continue;
            $sweep = ($value / $total) * 360;
            $color = self::getColor($idx);
            $allocColor = imagecolorallocate($image, $color[0], $color[1], $color[2]);
            
            imagefilledarc($image, $cx, $cy, $radius * 2, $radius * 2, $startAngle, $startAngle + $sweep, $allocColor, IMG_ARC_PIE);
            imagearc($image, $cx, $cy, $radius * 2, $radius * 2, $startAngle, $startAngle + $sweep, $dark);
            
            // Label
            $midAngle = $startAngle + $sweep / 2;
            $rad = deg2rad($midAngle);
            $lx = $cx + (int)(($radius + 20) * cos($rad));
            $ly = $cy + (int)(($radius + 20) * sin($rad));
            
            $pct = round(($value / $total) * 100);
            $label = $labels[$idx] . ' ' . $pct . '%';
            
            // Adjust for text alignment
            if ($lx > $cx) $lx += 2; else $lx -= strlen($label) * imagefontwidth(2) + 2;
            imagestring($image, 2, $lx, $ly - 4, $label, $dark);
            
            $startAngle += $sweep;
        }

        // Legend on right
        $legendX = 300;
        $legendY = 50;
        foreach ($values as $idx => $value) {
            if ($value == 0) continue;
            $color = self::getColor($idx);
            $allocColor = imagecolorallocate($image, $color[0], $color[1], $color[2]);
            imagefilledrectangle($image, $legendX, $legendY + $idx * 20, $legendX + 12, $legendY + $idx * 20 + 12, $allocColor);
            imagerectangle($image, $legendX, $legendY + $idx * 20, $legendX + 12, $legendY + $idx * 20 + 12, $dark);
            imagestring($image, 2, $legendX + 16, $legendY + $idx * 20, $labels[$idx], $dark);
        }

        return self::output($image);
    }

    /**
     * Generate a donut chart for event types
     */
    public static function donutChart(array $data, string $title): string
    {
        $w = 440; $h = 280;
        $image = self::createCanvas($w, $h);
        
        $white = imagecolorallocate($image, 255, 255, 255);
        $dark = imagecolorallocate($image, 15, 23, 42);
        $gray = imagecolorallocate($image, 100, 116, 139);
        
        self::drawTitle($image, $title, $w, 30, $dark);

        if (empty($data) || array_sum($data) == 0) {
            self::drawNoData($image, $w, $h, $gray);
            return self::output($image);
        }

        $total = array_sum($data);
        $cx = 160; $cy = 155; $outerR = 100; $innerR = 50;
        $startAngle = 0;

        $labels = array_keys($data);
        $values = array_values($data);

        foreach ($values as $idx => $value) {
            if ($value == 0) continue;
            $sweep = ($value / $total) * 360;
            $color = self::getColor($idx);
            $allocColor = imagecolorallocate($image, $color[0], $color[1], $color[2]);
            
            // Draw donut segment (outer pie minus inner circle)
            imagefilledarc($image, $cx, $cy, $outerR * 2, $outerR * 2, $startAngle, $startAngle + $sweep, $allocColor, IMG_ARC_PIE);
            imagearc($image, $cx, $cy, $outerR * 2, $outerR * 2, $startAngle, $startAngle + $sweep, $dark);
            
            // Percentage label
            $midAngle = $startAngle + $sweep / 2;
            $rad = deg2rad($midAngle);
            $labelR = ($outerR + $innerR) / 2;
            $lx = $cx + (int)($labelR * cos($rad)) - 4;
            $ly = $cy + (int)($labelR * sin($rad)) - 4;
            
            $pct = round(($value / $total) * 100);
            if ($pct > 5) {
                imagestring($image, 2, $lx, $ly, $pct . '%', $white);
            }
            
            $startAngle += $sweep;
        }

        // Inner white circle for donut effect
        imagefilledellipse($image, $cx, $cy, $innerR * 2, $innerR * 2, $white);
        imageellipse($image, $cx, $cy, $innerR * 2, $innerR * 2, $dark);

        // Legend on right
        $legendX = 300;
        $legendY = 50;
        foreach ($values as $idx => $value) {
            if ($value == 0) continue;
            $color = self::getColor($idx);
            $allocColor = imagecolorallocate($image, $color[0], $color[1], $color[2]);
            imagefilledrectangle($image, $legendX, $legendY + $idx * 20, $legendX + 12, $legendY + $idx * 20 + 12, $allocColor);
            imagerectangle($image, $legendX, $legendY + $idx * 20, $legendX + 12, $legendY + $idx * 20 + 12, $dark);
            imagestring($image, 2, $legendX + 16, $legendY + $idx * 20, $labels[$idx], $dark);
        }

        return self::output($image);
    }

    protected static function createCanvas(int $w, int $h): \GdImage
    {
        $image = imagecreatetruecolor($w, $h);
        imagefill($image, 0, 0, imagecolorallocate($image, 255, 255, 255));
        return $image;
    }

    protected static function drawTitle(\GdImage $image, string $title, int $w, int $tm, int $color): void
    {
        $fontSize = 5;
        $tw = strlen($title) * imagefontwidth($fontSize);
        $tx = (int)(($w - $tw) / 2);
        imagestring($image, $fontSize, max(0, $tx), 5, $title, $color);
    }

    protected static function drawNoData(\GdImage $image, int $w, int $h, int $color): void
    {
        $msg = 'Tidak Ada Data';
        $tx = (int)(($w - strlen($msg) * imagefontwidth(4)) / 2);
        $ty = (int)(($h - imagefontheight(4)) / 2);
        imagestring($image, 4, max(0, $tx), $ty, $msg, $color);
    }

    protected static function getColor(int $index): array
    {
        return self::$chartColors[$index % count(self::$chartColors)];
    }

    protected static function shortNumber(float $value): string
    {
        if ($value >= 1000000000) return number_format($value / 1000000000, 1) . 'M';
        if ($value >= 1000000) return number_format($value / 1000000, 1) . 'JT';
        if ($value >= 1000) return number_format($value / 1000, 1) . 'RB';
        if ($value == (int)$value) return (string)(int)$value;
        return number_format($value, 0);
    }

    protected static function output(\GdImage $image): string
    {
        ob_start();
        imagepng($image);
        $data = ob_get_clean();
        imagedestroy($image);
        return 'data:image/png;base64,' . base64_encode($data);
    }
}
