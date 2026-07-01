<?php
function writeFile(\, \) {
    \ = 'D:/Kelompok6_PBL2D/' . \;
    \ = dirname(\);
    if (!is_dir(\)) {
        mkdir(\, 0755, true);
    }
    file_put_contents(\, \);
    echo " Created: \\n\;
}
writeFile('test.txt', 'hello');
echo \done\n\;
