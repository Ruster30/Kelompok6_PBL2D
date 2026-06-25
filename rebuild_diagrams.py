import xml.etree.ElementTree as ET
import uuid
import datetime

tree = ET.parse(r'd:\SEMESTER 4\pemrograman-web-framework\PBL\Kelompok6_PBL2D\ActivityDiagram.xml')
root = tree.getroot()
ns = {
    'uml': 'http://schema.omg.org/spec/UML/2.1',
    'xmi': 'http://schema.omg.org/spec/XMI/2.1'
}

ET.register_namespace('', 'http://schema.omg.org/spec/UML/2.1')
ET.register_namespace('xmi', 'http://schema.omg.org/spec/XMI/2.1')
ET.register_namespace('uml', 'http://schema.omg.org/spec/UML/2.1')

# --- 1. CLEANUP PREVIOUS DIAGRAMS ---
diagram_names_to_delete = [
    "UC205 - Melihat Proposal dan RAB",
    "UC206 - Melihat Surat Kontrak dan Invoice",
    "UC207 - Menerima Notifikasi Terkait Proses Event",
    "UC208 - Upload Bukti Pembayaran",
    "UC209 - Melihat Laporan Akhir Event",
    "UC301 - Menerima Notifikasi Event",
    "UC302 - Melihat Timeline Event",
    "UC303 - Melaksanakan Tugas Event"
]

ext_diagrams = root.find('.//xmi:Extension/diagrams', ns)
ext_elements = root.find('.//xmi:Extension/elements', ns)

activity_elem = None
for pe in root.findall('.//packagedElement'):
    if pe.attrib.get('{http://schema.omg.org/spec/XMI/2.1}type') == 'uml:Activity':
        activity_elem = pe
        break

if activity_elem is None:
    print("Activity not found")
    exit(1)

ids_to_delete = set()
diags_to_remove = []

for diag in ext_diagrams.findall('diagram'):
    props = diag.find('properties')
    if props is not None and props.get('name') in diagram_names_to_delete:
        diags_to_remove.append(diag)
        for el in diag.findall('elements/element'):
            subj = el.get('subject')
            if subj:
                ids_to_delete.add(subj)

for d in diags_to_remove:
    ext_diagrams.remove(d)

# remove from activity
for child in list(activity_elem):
    cid = child.get('{http://schema.omg.org/spec/XMI/2.1}id')
    if cid in ids_to_delete:
        activity_elem.remove(child)

# remove from ext_elements
for child in list(ext_elements):
    cid = child.get('{http://schema.omg.org/spec/XMI/2.1}idref')
    if cid in ids_to_delete:
        ext_elements.remove(child)


# --- 2. BUILD NEW DIAGRAMS WITH CORRECT STRUCTURE ---
pkg_id = 'EAPK_9A6E8E2D_9265_40c5_860F_14C21708CA5D'
now_str = datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')

def make_uuid():
    return "EAID_" + str(uuid.uuid4()).upper().replace('-', '_')

diagrams_def = [
    {
        "name": "UC205 - Melihat Proposal dan RAB",
        "lanes": ["Klien", "Sistem"],
        "edges": [
            ("Mulai_K", "Buka_Menu"),
            ("Buka_Menu", "Tersedia_Dec"),
            ("Tersedia_Dec", "Tdk_Tersedia", "Tidak"),
            ("Tdk_Tersedia", "Selesai_S1"),
            ("Tersedia_Dec", "Tampil_Daftar", "Ya"),
            ("Tampil_Daftar", "Pilih_File"),
            ("Pilih_File", "Muat_Dec"),
            ("Muat_Dec", "Tdk_Muat", "Tidak"),
            ("Tdk_Muat", "Selesai_S2"),
            ("Muat_Dec", "Tampil_File", "Ya"),
            ("Tampil_File", "Baca_File"),
            ("Baca_File", "Klik_DL"),
            ("Klik_DL", "Unduh"),
            ("Unduh", "Selesai_S3")
        ],
        "nodes": {
            "Mulai_K": ("uml:InitialNode", "Klien", "Mulai", 100),
            "Buka_Menu": ("uml:Action", "Klien", "Membuka menu proposal dan RAB", 101),
            "Tersedia_Dec": ("uml:DecisionNode", "Sistem", "Proposal dan RAB tersedia?", 102),
            "Tdk_Tersedia": ("uml:Action", "Sistem", "Menampilkan informasi bahwa proposal dan RAB belum tersedia", 101),
            "Selesai_S1": ("uml:ActivityFinalNode", "Sistem", "Selesai", 101),
            "Tampil_Daftar": ("uml:Action", "Sistem", "Menampilkan daftar file proposal dan RAB", 101),
            "Pilih_File": ("uml:Action", "Klien", "Memilih file proposal atau RAB", 101),
            "Muat_Dec": ("uml:DecisionNode", "Sistem", "File berhasil dimuat?", 102),
            "Tdk_Muat": ("uml:Action", "Sistem", "Menampilkan pesan gagal memuat file", 101),
            "Selesai_S2": ("uml:ActivityFinalNode", "Sistem", "Selesai", 101),
            "Tampil_File": ("uml:Action", "Sistem", "Menampilkan file proposal atau RAB", 101),
            "Baca_File": ("uml:Action", "Klien", "Membaca isi file proposal atau RAB", 101),
            "Klik_DL": ("uml:Action", "Klien", "Memilih tombol \"Download\"", 101),
            "Unduh": ("uml:Action", "Sistem", "Mengunduh file dalam format PDF", 101),
            "Selesai_S3": ("uml:ActivityFinalNode", "Sistem", "Selesai", 101)
        }
    },
    {
        "name": "UC206 - Melihat Surat Kontrak dan Invoice",
        "lanes": ["Klien", "Sistem"],
        "edges": [
            ("Mulai_K", "Buka_Menu"),
            ("Buka_Menu", "Tersedia_Dec"),
            ("Tersedia_Dec", "Tdk_Tersedia", "Tidak"),
            ("Tdk_Tersedia", "Selesai_S1"),
            ("Tersedia_Dec", "Tampil_Daftar", "Ya"),
            ("Tampil_Daftar", "Pilih_Doc"),
            ("Pilih_Doc", "Muat_Dec"),
            ("Muat_Dec", "Tdk_Muat", "Tidak"),
            ("Tdk_Muat", "Selesai_S2"),
            ("Muat_Dec", "Tampil_Doc", "Ya"),
            ("Tampil_Doc", "Baca_Doc"),
            ("Baca_Doc", "Klik_DL"),
            ("Klik_DL", "Unduh"),
            ("Unduh", "Selesai_S3")
        ],
        "nodes": {
            "Mulai_K": ("uml:InitialNode", "Klien", "Mulai", 100),
            "Buka_Menu": ("uml:Action", "Klien", "Membuka menu surat kontrak atau menu invoice", 101),
            "Tersedia_Dec": ("uml:DecisionNode", "Sistem", "Dokumen tersedia?", 102),
            "Tdk_Tersedia": ("uml:Action", "Sistem", "Menampilkan informasi bahwa dokumen belum tersedia", 101),
            "Selesai_S1": ("uml:ActivityFinalNode", "Sistem", "Selesai", 101),
            "Tampil_Daftar": ("uml:Action", "Sistem", "Menampilkan daftar dokumen", 101),
            "Pilih_Doc": ("uml:Action", "Klien", "Memilih dokumen yang ingin dilihat", 101),
            "Muat_Dec": ("uml:DecisionNode", "Sistem", "Dokumen berhasil dimuat?", 102),
            "Tdk_Muat": ("uml:Action", "Sistem", "Menampilkan pesan gagal memuat dokumen", 101),
            "Selesai_S2": ("uml:ActivityFinalNode", "Sistem", "Selesai", 101),
            "Tampil_Doc": ("uml:Action", "Sistem", "Menampilkan dokumen yang dipilih", 101),
            "Baca_Doc": ("uml:Action", "Klien", "Membaca isi dokumen", 101),
            "Klik_DL": ("uml:Action", "Klien", "Memilih tombol \"Download\"", 101),
            "Unduh": ("uml:Action", "Sistem", "Mengunduh dokumen dalam format PDF", 101),
            "Selesai_S3": ("uml:ActivityFinalNode", "Sistem", "Selesai", 101)
        }
    },
    {
        "name": "UC207 - Menerima Notifikasi Terkait Proses Event",
        "lanes": ["Klien", "Sistem"],
        "edges": [
            ("Mulai_S", "Kirim_Notif"),
            ("Kirim_Notif", "Buka_Menu"),
            ("Buka_Menu", "Tampil_Notif"),
            ("Tampil_Notif", "Baca_Notif"),
            ("Baca_Notif", "Selesai_K")
        ],
        "nodes": {
            "Mulai_S": ("uml:InitialNode", "Sistem", "Mulai", 100),
            "Kirim_Notif": ("uml:Action", "Sistem", "Mengirim notifikasi", 101),
            "Buka_Menu": ("uml:Action", "Klien", "Membuka menu notifikasi", 101),
            "Tampil_Notif": ("uml:Action", "Sistem", "Menampilkan daftar notifikasi", 101),
            "Baca_Notif": ("uml:Action", "Klien", "Membaca notifikasi", 101),
            "Selesai_K": ("uml:ActivityFinalNode", "Klien", "Selesai", 101)
        }
    },
    {
        "name": "UC208 - Upload Bukti Pembayaran",
        "lanes": ["Klien", "Sistem"],
        "edges": [
            ("Mulai_K", "Buka_Menu"),
            ("Buka_Menu", "Pilih_Inv"),
            ("Pilih_Inv", "Klik_Up"),
            ("Klik_Up", "Pilih_File"),
            ("Pilih_File", "Klik_Kirim"),
            ("Klik_Kirim", "Valid_Dec"),
            ("Valid_Dec", "Tdk_Valid", "Tidak"),
            ("Tdk_Valid", "Selesai_S1"),
            ("Valid_Dec", "Simpan", "Ya"),
            ("Simpan", "Tampil_Sukses"),
            ("Tampil_Sukses", "Selesai_S2")
        ],
        "nodes": {
            "Mulai_K": ("uml:InitialNode", "Klien", "Mulai", 100),
            "Buka_Menu": ("uml:Action", "Klien", "Membuka menu invoice", 101),
            "Pilih_Inv": ("uml:Action", "Klien", "Memilih invoice", 101),
            "Klik_Up": ("uml:Action", "Klien", "Menekan tombol upload bukti pembayaran", 101),
            "Pilih_File": ("uml:Action", "Klien", "Memilih file bukti pembayaran", 101),
            "Klik_Kirim": ("uml:Action", "Klien", "Menekan tombol kirim", 101),
            "Valid_Dec": ("uml:DecisionNode", "Sistem", "Format file sesuai?", 102),
            "Tdk_Valid": ("uml:Action", "Sistem", "Menampilkan pesan kesalahan format file", 101),
            "Selesai_S1": ("uml:ActivityFinalNode", "Sistem", "Selesai", 101),
            "Simpan": ("uml:Action", "Sistem", "Menyimpan bukti pembayaran", 101),
            "Tampil_Sukses": ("uml:Action", "Sistem", "Menampilkan pesan berhasil", 101),
            "Selesai_S2": ("uml:ActivityFinalNode", "Sistem", "Selesai", 101)
        }
    },
    {
        "name": "UC209 - Melihat Laporan Akhir Event",
        "lanes": ["Klien", "Sistem"],
        "edges": [
            ("Mulai_K", "Buka_Menu"),
            ("Buka_Menu", "Tersedia_Dec"),
            ("Tersedia_Dec", "Tdk_Tersedia", "Tidak"),
            ("Tdk_Tersedia", "Selesai_S1"),
            ("Tersedia_Dec", "Tampil_Daftar", "Ya"),
            ("Tampil_Daftar", "Pilih_Lap"),
            ("Pilih_Lap", "Muat_Dec"),
            ("Muat_Dec", "Tdk_Muat", "Tidak"),
            ("Tdk_Muat", "Selesai_S2"),
            ("Muat_Dec", "Tampil_Lap", "Ya"),
            ("Tampil_Lap", "Baca_Lap"),
            ("Baca_Lap", "Klik_DL"),
            ("Klik_DL", "Unduh"),
            ("Unduh", "Selesai_S3")
        ],
        "nodes": {
            "Mulai_K": ("uml:InitialNode", "Klien", "Mulai", 100),
            "Buka_Menu": ("uml:Action", "Klien", "Membuka menu laporan akhir", 101),
            "Tersedia_Dec": ("uml:DecisionNode", "Sistem", "Laporan akhir tersedia?", 102),
            "Tdk_Tersedia": ("uml:Action", "Sistem", "Menampilkan informasi laporan akhir belum tersedia", 101),
            "Selesai_S1": ("uml:ActivityFinalNode", "Sistem", "Selesai", 101),
            "Tampil_Daftar": ("uml:Action", "Sistem", "Menampilkan daftar laporan akhir", 101),
            "Pilih_Lap": ("uml:Action", "Klien", "Memilih laporan akhir yang ingin dilihat", 101),
            "Muat_Dec": ("uml:DecisionNode", "Sistem", "File berhasil dimuat?", 102),
            "Tdk_Muat": ("uml:Action", "Sistem", "Menampilkan pesan gagal memuat laporan akhir", 101),
            "Selesai_S2": ("uml:ActivityFinalNode", "Sistem", "Selesai", 101),
            "Tampil_Lap": ("uml:Action", "Sistem", "Menampilkan file laporan akhir", 101),
            "Baca_Lap": ("uml:Action", "Klien", "Membaca isi laporan akhir", 101),
            "Klik_DL": ("uml:Action", "Klien", "Memilih tombol \"Download\"", 101),
            "Unduh": ("uml:Action", "Sistem", "Mengunduh laporan akhir dalam format PDF", 101),
            "Selesai_S3": ("uml:ActivityFinalNode", "Sistem", "Selesai", 101)
        }
    },
    {
        "name": "UC301 - Menerima Notifikasi Event",
        "lanes": ["Vendor", "Sistem"],
        "edges": [
            ("Mulai_S", "Kirim_Notif"),
            ("Kirim_Notif", "Buka_Menu"),
            ("Buka_Menu", "Ada_Dec"),
            ("Ada_Dec", "Tdk_Ada", "Tidak"),
            ("Tdk_Ada", "Selesai_S1"),
            ("Ada_Dec", "Tampil_Daftar", "Ya"),
            ("Tampil_Daftar", "Pilih_Notif"),
            ("Pilih_Notif", "Tampil_Detail"),
            ("Tampil_Detail", "Baca_Detail"),
            ("Baca_Detail", "Selesai_V")
        ],
        "nodes": {
            "Mulai_S": ("uml:InitialNode", "Sistem", "Mulai", 100),
            "Kirim_Notif": ("uml:Action", "Sistem", "Mengirim notifikasi penugasan event", 101),
            "Buka_Menu": ("uml:Action", "Vendor", "Membuka menu notifikasi", 101),
            "Ada_Dec": ("uml:DecisionNode", "Sistem", "Ada notifikasi event?", 102),
            "Tdk_Ada": ("uml:Action", "Sistem", "Menampilkan informasi tidak ada notifikasi event", 101),
            "Selesai_S1": ("uml:ActivityFinalNode", "Sistem", "Selesai", 101),
            "Tampil_Daftar": ("uml:Action", "Sistem", "Menampilkan daftar notifikasi event", 101),
            "Pilih_Notif": ("uml:Action", "Vendor", "Memilih notifikasi event", 101),
            "Tampil_Detail": ("uml:Action", "Sistem", "Menampilkan detail penugasan event", 101),
            "Baca_Detail": ("uml:Action", "Vendor", "Membaca informasi event dan tugas", 101),
            "Selesai_V": ("uml:ActivityFinalNode", "Vendor", "Selesai", 101)
        }
    },
    {
        "name": "UC302 - Melihat Timeline Event",
        "lanes": ["Vendor", "Sistem"],
        "edges": [
            ("Mulai_V", "Buka_Menu"),
            ("Buka_Menu", "Tampil_Daftar"),
            ("Tampil_Daftar", "Pilih_Event"),
            ("Pilih_Event", "Tersedia_Dec"),
            ("Tersedia_Dec", "Tdk_Tersedia", "Tidak"),
            ("Tdk_Tersedia", "Selesai_S1"),
            ("Tersedia_Dec", "Tampil_Timeline", "Ya"),
            ("Tampil_Timeline", "Lihat_Jadwal"),
            ("Lihat_Jadwal", "Pahami_Jadwal"),
            ("Pahami_Jadwal", "Selesai_V")
        ],
        "nodes": {
            "Mulai_V": ("uml:InitialNode", "Vendor", "Mulai", 100),
            "Buka_Menu": ("uml:Action", "Vendor", "Membuka menu timeline event", 101),
            "Tampil_Daftar": ("uml:Action", "Sistem", "Menampilkan daftar event", 101),
            "Pilih_Event": ("uml:Action", "Vendor", "Memilih event", 101),
            "Tersedia_Dec": ("uml:DecisionNode", "Sistem", "Timeline event tersedia?", 102),
            "Tdk_Tersedia": ("uml:Action", "Sistem", "Menampilkan informasi timeline event belum tersedia", 101),
            "Selesai_S1": ("uml:ActivityFinalNode", "Sistem", "Selesai", 101),
            "Tampil_Timeline": ("uml:Action", "Sistem", "Menampilkan timeline event", 101),
            "Lihat_Jadwal": ("uml:Action", "Vendor", "Melihat jadwal kegiatan event", 101),
            "Pahami_Jadwal": ("uml:Action", "Vendor", "Memahami jadwal pelaksanaan tugas event", 101),
            "Selesai_V": ("uml:ActivityFinalNode", "Vendor", "Selesai", 101)
        }
    },
    {
        "name": "UC303 - Melaksanakan Tugas Event",
        "lanes": ["Vendor", "Sistem"],
        "edges": [
            ("Mulai_V", "Buka_Menu"),
            ("Buka_Menu", "Tampil_Daftar"),
            ("Tampil_Daftar", "Pilih_Tugas"),
            ("Pilih_Tugas", "Tampil_Detail"),
            ("Tampil_Detail", "Laksana"),
            ("Laksana", "Status_Dec"),
            ("Status_Dec", "Upd_Selesai", "Ya"),
            ("Status_Dec", "Upd_Proses", "Tidak"),
            ("Upd_Selesai", "Simpan_S"),
            ("Upd_Proses", "Simpan_S"),
            ("Simpan_S", "Tampil_Sukses"),
            ("Tampil_Sukses", "Selesai_S")
        ],
        "nodes": {
            "Mulai_V": ("uml:InitialNode", "Vendor", "Mulai", 100),
            "Buka_Menu": ("uml:Action", "Vendor", "Membuka menu tugas event", 101),
            "Tampil_Daftar": ("uml:Action", "Sistem", "Menampilkan daftar tugas event", 101),
            "Pilih_Tugas": ("uml:Action", "Vendor", "Memilih tugas event", 101),
            "Tampil_Detail": ("uml:Action", "Sistem", "Menampilkan detail tugas event", 101),
            "Laksana": ("uml:Action", "Vendor", "Melaksanakan tugas sesuai penugasan", 101),
            "Status_Dec": ("uml:DecisionNode", "Vendor", "Tugas sudah selesai?", 102),
            "Upd_Selesai": ("uml:Action", "Vendor", "Memperbarui status tugas menjadi Selesai", 101),
            "Upd_Proses": ("uml:Action", "Vendor", "Memperbarui status tugas menjadi Dalam Proses", 101),
            "Simpan_S": ("uml:Action", "Sistem", "Menyimpan perubahan status tugas", 101),
            "Tampil_Sukses": ("uml:Action", "Sistem", "Menampilkan informasi status berhasil diperbarui", 101),
            "Selesai_S": ("uml:ActivityFinalNode", "Sistem", "Selesai", 101)
        }
    }
]

for d_def in diagrams_def:
    diag_id = make_uuid()
    
    # 1. Create swimlanes mapping
    lane_map = {}
    
    # Create swimlanes in UML Activity
    for lane_name in d_def["lanes"]:
        swimlane_id = make_uuid()
        group = ET.SubElement(activity_elem, 'group')
        group.set('{http://schema.omg.org/spec/XMI/2.1}type', 'uml:ActivityPartition')
        group.set('{http://schema.omg.org/spec/XMI/2.1}id', swimlane_id)
        group.set('name', lane_name)
        group.set('visibility', 'public')
        lane_map[lane_name] = swimlane_id

    # Add diagram to xmi:Extension/diagrams
    diag = ET.SubElement(ext_diagrams, 'diagram')
    diag.set('xmi:id', diag_id)
    model = ET.SubElement(diag, 'model')
    model.set('package', pkg_id)
    model.set('owner', pkg_id)
    props = ET.SubElement(diag, 'properties')
    props.set('name', d_def['name'])
    props.set('type', 'Activity')
    proj = ET.SubElement(diag, 'project')
    proj.set('author', 'Auto')
    proj.set('version', '1.0')
    proj.set('created', now_str)
    proj.set('modified', now_str)
    diag_elements = ET.SubElement(diag, 'elements')

    # Add swimlanes to diagrams and extension
    seq = 1
    for i, lane_name in enumerate(d_def["lanes"]):
        lane_id = lane_map[lane_name]
        el = ET.SubElement(diag_elements, 'element')
        el.set('geometry', f'Left={50 + i*300};Top=20;Right={300 + i*300};Bottom=800;')
        el.set('subject', lane_id)
        el.set('seqno', str(seq))
        el.set('style', 'Dockable=on;VPartition=1;')
        seq += 1
        
        ext_el = ET.SubElement(ext_elements, 'element')
        ext_el.set('{http://schema.omg.org/spec/XMI/2.1}idref', lane_id)
        ext_el.set('{http://schema.omg.org/spec/XMI/2.1}type', 'uml:ActivityPartition')
        ext_el.set('name', lane_name)
        props = ET.SubElement(ext_el, 'properties')
        props.set('sType', 'ActivityPartition')

    # 2. Add Nodes
    node_map = {} # internal_name -> node_id
    
    # Simple vertical layout tracker per lane
    top_y_tracker = {lane: 50 for lane in d_def["lanes"]}
    
    for n_key, n_data in d_def["nodes"].items():
        node_type, lane, name, ea_stype = n_data
        node_id = make_uuid()
        node_map[n_key] = node_id
        
        # assign to group
        lane_group = activity_elem.find(f".//group[@{{http://schema.omg.org/spec/XMI/2.1}}id='{lane_map[lane]}']")
        n_ref = ET.SubElement(lane_group, 'node')
        n_ref.set('{http://schema.omg.org/spec/XMI/2.1}idref', node_id)
        
        # create node
        n_elem = ET.SubElement(activity_elem, 'node')
        n_elem.set('{http://schema.omg.org/spec/XMI/2.1}id', node_id)
        n_elem.set('{http://schema.omg.org/spec/XMI/2.1}type', node_type)
        n_elem.set('name', name)
        n_elem.set('visibility', 'public')
        
        # extension element
        ext_el = ET.SubElement(ext_elements, 'element')
        ext_el.set('{http://schema.omg.org/spec/XMI/2.1}idref', node_id)
        ext_el.set('{http://schema.omg.org/spec/XMI/2.1}type', node_type)
        ext_el.set('name', name)
        props = ET.SubElement(ext_el, 'properties')
        
        stype = str(ea_stype) if type(ea_stype) == int else ea_stype
        if node_type == "uml:DecisionNode":
            ntype = "Decision"
            stype = "Decision"
        elif node_type == "uml:Action":
            ntype = "Action"
            stype = "Action"
        else:
            ntype = "StateNode"
            
        props.set('sType', stype)
        props.set('nType', ntype)
        
        # diagram element
        top_y = top_y_tracker[lane]
        lane_idx = d_def["lanes"].index(lane)
        left_x = 100 + lane_idx * 300
        el = ET.SubElement(diag_elements, 'element')
        el.set('geometry', f'Left={left_x};Top={top_y};Right={left_x+100};Bottom={top_y+40};')
        el.set('subject', node_id)
        el.set('seqno', str(seq))
        seq += 1
        
        top_y_tracker[lane] += 80

    # 3. Add Edges
    for edge_def in d_def["edges"]:
        source_key = edge_def[0]
        target_key = edge_def[1]
        name = edge_def[2] if len(edge_def) > 2 else ""
        
        source_id = node_map[source_key]
        target_id = node_map[target_key]
        
        edge_id = make_uuid()
        
        # uml element
        edge = ET.SubElement(activity_elem, 'edge')
        edge.set('{http://schema.omg.org/spec/XMI/2.1}type', 'uml:ControlFlow')
        edge.set('{http://schema.omg.org/spec/XMI/2.1}id', edge_id)
        edge.set('visibility', 'public')
        edge.set('source', source_id)
        edge.set('target', target_id)
        if name:
            edge.set('name', name)
            
        # Add incoming/outgoing
        src_n = activity_elem.find(f".//node[@{{http://schema.omg.org/spec/XMI/2.1}}id='{source_id}']")
        if src_n is not None:
            out_ref = ET.SubElement(src_n, 'outgoing')
            out_ref.set('{http://schema.omg.org/spec/XMI/2.1}idref', edge_id)
            
        tgt_n = activity_elem.find(f".//node[@{{http://schema.omg.org/spec/XMI/2.1}}id='{target_id}']")
        if tgt_n is not None:
            in_ref = ET.SubElement(tgt_n, 'incoming')
            in_ref.set('{http://schema.omg.org/spec/XMI/2.1}idref', edge_id)
            
        # diagram extension
        el = ET.SubElement(diag_elements, 'element')
        el.set('subject', edge_id)
        el.set('style', 'Mode=3;') # Orthogonal routing
        
        # element extension
        ext_el = ET.SubElement(ext_elements, 'connector')
        ext_el.set('{http://schema.omg.org/spec/XMI/2.1}idref', edge_id)
        source_node = ET.SubElement(ext_el, 'source')
        source_node.set('{http://schema.omg.org/spec/XMI/2.1}idref', source_id)
        target_node = ET.SubElement(ext_el, 'target')
        target_node.set('{http://schema.omg.org/spec/XMI/2.1}idref', target_id)
        props = ET.SubElement(ext_el, 'properties')
        props.set('ea_type', 'ControlFlow')
        if name:
            lbl = ET.SubElement(ext_el, 'labels')
            lbl.set('mt', name)


out_path = r'd:\SEMESTER 4\pemrograman-web-framework\PBL\Kelompok6_PBL2D\ActivityDiagram.xml'
tree.write(out_path, encoding='windows-1252', xml_declaration=True)
print("Successfully rebuilt diagrams!")
