import sys
path = "D:/Kelompok6_PBL2D/app/Services/DocumentBuilderService.php"
with open(path, "r", encoding="utf-8") as f:
    content = f.read()

# Replace sendToClient Document::create
old1 = '        $document = Document::create([\n'
old1 += "            'event_id'  => $event->id,\n"
old1 += "            'user_id'   => auth()->id(),\n"
old1 += "            'nama_file' => $this->labelJenis($jenisDokumen) . ' - ' . $event->nama_event,\n"
old1 += "            'file_path' => $path,\n"
old1 += "            'tipe'      => $tipeEnum,\n"
old1 += "            'document_source' => DocumentSource::Generated,\n"
old1 += '        ]);'

new1 = '        $document = $this->storeGeneratedDocument(\n'
new1 += '            event:   $event,\n'
new1 += '            userId:  auth()->id(),\n'
new1 += "            namaFile: $this->labelJenis($jenisDokumen) . ' - ' . $event->nama_event,\n"
new1 += '            filePath: $path,\n'
new1 += '            tipe:    $tipeEnum,\n'
new1 += '        );'

if old1 in content:
    content = content.replace(old1, new1)
    print("sendToClient: REPLACED")
else:
    print("sendToClient: NOT FOUND")

# Replace generateAndSaveKwitansi Document::create
old2 = '        $document = Document::create([\n'
old2 += "            'event_id'  => $event->id,\n"
old2 += "            'user_id'   => auth()->id() ?? 1,\n"
old2 += "            'nama_file' => 'Kwitansi - ' . $event->nama_event,\n"
old2 += "            'file_path' => $path,\n"
old2 += "            'tipe'      => 'kwitansi',\n"
old2 += "            'document_source' => DocumentSource::Generated,\n"
old2 += '        ]);'

new2 = '        $document = $this->storeGeneratedDocument(\n'
new2 += '            event:   $event,\n'
new2 += '            userId:  auth()->id() ?? 1,\n'
new2 += "            namaFile: 'Kwitansi - ' . $event->nama_event,\n"
new2 += '            filePath: $path,\n'
new2 += "            tipe:    'kwitansi',\n"
new2 += '        );'

if old2 in content:
    content = content.replace(old2, new2)
    print("generateAndSaveKwitansi: REPLACED")
else:
    print("generateAndSaveKwitansi: NOT FOUND")

with open(path, "w", encoding="utf-8") as f:
    f.write(content)
print("DONE")