with open(r'd:\SEMESTER 4\pemrograman-web-framework\PBL\Kelompok6_PBL2D\ActivityDiagram.xml', 'r', encoding='windows-1252') as f:
    content = f.read()

# Replace namespace prefixes
content = content.replace('<ns0:', '<xmi:')
content = content.replace('</ns0:', '</xmi:')
content = content.replace(' ns0:', ' xmi:')
content = content.replace('xmlns:ns0=', 'xmlns:xmi=')

content = content.replace('<ns1:', '<uml:')
content = content.replace('</ns1:', '</uml:')
content = content.replace(' ns1:', ' uml:')
content = content.replace('xmlns:ns1=', 'xmlns:uml=')

content = content.replace('<diagram xmlns:ns0="http://schema.omg.org/spec/XMI/2.1" xmi:id', '<diagram xmi:id')
content = content.replace('xmlns:ns0="http://schema.omg.org/spec/XMI/2.1"', '')

with open(r'd:\SEMESTER 4\pemrograman-web-framework\PBL\Kelompok6_PBL2D\ActivityDiagram.xml', 'w', encoding='windows-1252') as f:
    f.write(content)

print("Namespaces fixed!")
