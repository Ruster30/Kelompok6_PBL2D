const fs = require('fs');
const path = require('path');
const baseDir = 'D:/Kelompok6_PBL2D';
function w(relPath, content) {
    const fp = path.join(baseDir, relPath);
    fs.mkdirSync(path.dirname(fp), { recursive: true });
    fs.writeFileSync(fp, content, 'utf8');
    console.log('Created:', relPath);
}
