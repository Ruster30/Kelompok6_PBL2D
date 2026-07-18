const fs = require('fs');
let content = fs.readFileSync('D:/Kelompok6_PBL2D/resources/views/admin/analytics/pdf.blade.php', 'utf-8').replace(/\r\n/g, '\n');
let lines = content.split('\n');

// First find exact line numbers
let bodyIdx = -1, infoCardIdx = -1, headerRightIdx = -1;
for (let i = 0; i < lines.length; i++) {
    if (lines[i].includes('<body>')) bodyIdx = i;
    if (lines[i].includes('<div class=\"info-card\">') && infoCardIdx === -1) infoCardIdx = i;
    if (lines[i].includes('class=\"header-right\"') && headerRightIdx === -1) headerRightIdx = i;
}

// Find the closing </td> for header-right
let outerTdIdx = -1;
for (let i = infoCardIdx; i < lines.length; i++) {
    let trimmed = lines[i].trim();
    // The closing </td> for header-right is at the same indentation as the opening <td>
    if (trimmed === '</td>') {
        let indent = lines[i].match(/^(\s*)/)[1];
        let openingIndent = lines[headerRightIdx].match(/^(\s*)/)[1];
        if (indent.length === openingIndent.length) {
            outerTdIdx = i;
            break;
        }
    }
}

console.log('body:', bodyIdx, 'header-right open:', headerRightIdx, 'info-card:', infoCardIdx, 'header-right close:', outerTdIdx);

// Build the result
let result = [];
for (let i = 0; i < lines.length; i++) {
    let line = lines[i];
    
    if (i === bodyIdx) {
        result.push(line);
        result.push('<div class=\"pdf-body\" style=\"width:92%; max-width:1100px; margin:0 auto;\">');
        continue;
    }
    
    if (line.includes('</body>')) {
        result.push('</div>');
        result.push(line);
        continue;
    }
    
    if (i === infoCardIdx) {
        // Insert clean info-card
        let indent = '                    ';
        result.push(indent + '<div class=\"info-card\">');
        result.push(indent + '    <div class=\"info-label\">Periode Laporan</div>');
        result.push(indent + '    <div class=\"info-value\">Tahun {{ \[\"year\"] ?? now()->year }}</div>');
        result.push(indent + '    <hr class=\"info-divider-hr\" />');
        result.push(indent + '    <div class=\"info-label\">Tanggal Cetak</div>');
        result.push(indent + '    <div class=\"info-value\">{{ now()->translatedFormat(\"d F Y\") }}</div>');
        result.push(indent + '</div>');
        
        // Skip to the outer </td>
        i = outerTdIdx;
        result.push(lines[i]); // keep the outer </td>
        continue;
    }
    
    result.push(line);
}

let output = result.join('\n').replace(/\n/g, '\r\n');
fs.writeFileSync('D:/Kelompok6_PBL2D/resources/views/admin/analytics/pdf.blade.php', output, 'utf-8');
console.log('Done. Lines: ' + result.length);