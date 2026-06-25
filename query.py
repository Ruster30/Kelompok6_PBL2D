import xml.etree.ElementTree as ET

tree = ET.parse(r'd:\SEMESTER 4\pemrograman-web-framework\PBL\Kelompok6_PBL2D\ActivityDiagram.xml')
root = tree.getroot()
ns = {
    'uml': 'http://schema.omg.org/spec/UML/2.1',
    'xmi': 'http://schema.omg.org/spec/XMI/2.1'
}

# Find groups named "Sistem"
sistem_groups = []
for group in root.findall('.//group', ns):
    if group.get('name') == 'Sistem':
        sistem_groups.append(group)

for sg in sistem_groups[:2]: # look at the first 2 diagrams with "Sistem"
    print(f"Group ID: {sg.get('{http://schema.omg.org/spec/XMI/2.1}id')}")
    # find nodes inside
    node_refs = sg.findall('node', ns)
    for ref in node_refs:
        ref_id = ref.get('{http://schema.omg.org/spec/XMI/2.1}idref')
        # find actual node
        node = root.find(f".//node[@{{http://schema.omg.org/spec/XMI/2.1}}id='{ref_id}']", ns)
        if node is not None:
            print(f"  - {node.get('name')} ({node.get('{http://schema.omg.org/spec/XMI/2.1}type')})")
