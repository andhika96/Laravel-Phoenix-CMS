"""Publish the final cross-project index, current-chat handoff, and verified reports."""
import collections
import datetime as dt
import hashlib
import importlib.util
import json
import re
import shutil
from pathlib import Path

BASE=Path(__file__).parent
GRAPH=BASE.parent/'graphify-all-projects-20261020'
OUTPUT=Path('C:/Users/aruna/Documents/Codex/2026-10-20/ch/outputs')
E=Path('E:/AI/Memories')
C=Path('C:/Users/aruna/.codex/memories/extensions/ad_hoc/notes')
CURRENT='01a15802-6823-7731-8e79-8c5a9aa0ee52'
def read(p):return json.loads(p.read_text(encoding='utf-8'))
def sha(p):return hashlib.sha256(p.read_bytes()).hexdigest()
def new(p,body):
    p.parent.mkdir(parents=True,exist_ok=True)
    data=body.encode('utf-8')
    if p.exists():assert p.read_bytes()==data,'Refuse overwrite: '+str(p)
    else:
        with p.open('xb') as stream:stream.write(data)

def main():
    rows=read(BASE/'prepared.json');manifest=read(BASE/'manifest.json');memory=read(BASE/'verification.json');graphs=read(GRAPH/'final-qc.json')
    assert not graphs['errors'] and len(graphs['results'])==10
    by_id={x['id']:x for x in manifest}
    confidence=sum((collections.Counter(r['confidence']) for r in graphs['results']),collections.Counter())
    changes={}
    for r in read(GRAPH/'inventory.json'):
        if r['status']=='planned':changes[r['root']]=read(Path(r['artifacts'])/'verification.json').get('changed_code_files',0)
    total_nodes=sum(r['nodes'] for r in graphs['results']);total_edges=sum(r['edges'] for r in graphs['results'])
    table=['| Project | Status Graphify | Node | Relasi |','|---|---|---:|---:|']
    for r in graphs['results']:
        count=changes.get(r['root'])
        status='Graph baru konfigurasi museum' if r['label']=='JPOG museum' else ('Graph baru; 8 file kode' if r['label']=='Verdant Vale RPG' else ('Diperbarui; '+str(count)+' file kode' if count else 'Diverifikasi; kode tidak berubah'))
        table.append('| '+r['label']+' | '+status+' | '+format(r['nodes'],',')+' | '+format(r['edges'],',')+' |')
    graph_table='\n'.join(table)
    skipped='\n'.join('- '+r['label']+' — '+r['root']+'; '+r['status'] for r in graphs['skipped'])
    note_name='20261020-all-projects-sync-handoff-'+CURRENT+'.md'
    handoff='\n'.join(['# Sinkronisasi memori dan Graphify lintas project','',
        'Tanggal: 20 Oktober 2026 (Asia/Jakarta).','Chat ID: '+CURRENT,'Project koordinasi: lintas project; artefak bersama berada pada project-artifacts Phoenix.','',
        '## Tujuan dan konteks awal','','Pengguna meminta seluruh chat yang belum tersimpan diarsipkan, catatan yang mendapat pesan baru diperbarui, kemudian Graphify setiap project disesuaikan dengan source aktif. Indeks 12 Oktober tersedia; ekspor 15 Oktober sebelumnya baru staging.','',
        '## Keputusan penting','','- Satu catatan Markdown per chat; pembaruan berupa catatan tambahan agar versi lama tetap utuh.','- E:/AI/Memories adalah memori utama; C:/Users/aruna/.codex/memories/extensions/ad_hoc/notes adalah salinan kompatibel.','- Chat di luar project tidak dibuatkan folder project-artifacts. Artefak Graphify, backup, dan laporan berada di project masing-masing.','- Pengguna secara eksplisit mengizinkan project-artifacts dalam D:/Arunika Map Studio dan D:/Codex-MultiWorkspace untuk catatan memori dan Graphify; kedua folder sudah tersedia.','- Project hilang tidak direkonstruksi; Graphify berjalan offline tanpa mengirim source atau graph ke layanan eksternal.','',
        '## Implementasi dan verifikasi memori','','- 192 chat terdahulu dicocokkan: 187 chat pengguna lokal, 3 chat cloud terbaca saat ini, dan 2 chat cloud dalam arsip terdahulu.','- Enam profil lokal tambahan diperiksa; tidak ditemukan Chat ID pengguna tambahan yang belum tercakup.','- 20 catatan baru dan 11 pembaruan disimpan sebagai 31 pasangan E/C; 161 chat lain tidak mendapat catatan duplikat karena isi tidak berubah.','- Chat aktif ini disimpan tersendiri; jumlah cakupan termasuk chat aktif menjadi 193.','- 6.036 pesan teks pengguna/laporan akhir menjadi bahan pencocokan. Riwayat yang terpotong direkonsiliasi dengan arsip 12 Oktober dan handoff historis.','- Seluruh halaman read_thread untuk enam chat tanpa rollout dibaca sampai hasMore=false; arsip aplikasi Codex/ChatGPT sama-sama kosong pada pemeriksaan ini.','- 395 file memori lama yang dirujuk tetap sesuai SHA-256; seluruh pasangan baru nonkosong dan identik.','',
        '## Graphify per project','',graph_table,'',
        'Total: '+str(total_nodes)+' node, '+str(total_edges)+' relasi; confidence '+json.dumps(dict(confidence))+'.','- 8 graph yang sudah ada diperbarui/disegarkan secara incremental; 2 graph baru dibuat untuk RPG dan konfigurasi museum JPOG.','- Backup graph lama diverifikasi sebelum penulisan. Metadata-only tidak dianggap AST atau bukti runtime.','- Integritas diperiksa: ID unik, endpoint valid, hash source/manifest sesuai, node yang tidak terdampak terjaga, HEAD/staged content/status aplikasi terjaga, HTML memakai script lokal.','- Ada 62 self-loop pada keluaran parser. Pemeriksaan receiver menandai 36 hubungan same-name pada objek berbeda sebagai AMBIGUOUS. Sisanya dipertahankan; beberapa merupakan recursion yang valid dan belum diaudit satu per satu.','- Diagnostik pasangan undirected tidak berarti relasi hilang: seluruh graph tersimpan directed dan pasangan arah berlawanan tetap terpelihara.','',
        '## Project dilewati','',skipped,'',
        '## File dibuat dan backup','','- Per-chat notes: 20261020-delta-<judul>-<chat-id>.md di E/C; 20 salinan handoff project juga diverifikasi pada project-artifacts masing-masing.','- Indeks utama: 20261020-all-projects-index.md di E/C.','- Artefak memori: '+str(BASE),'- Artefak Graphify: <project-root>/project-artifacts/graphify-all-projects-20261020.','- Output graph: <project-root>/graphify-out/{graph.json,graph.html,GRAPH_REPORT.md,manifest.json,metadata-manifest.json}; konfigurasi museum memakai metadata-manifest tanpa klaim AST binary.','- Backup awal: project-artifacts/graphify-all-projects-20261020/backup_20261020_*/graphify-out, disertai backup-manifest.json.','- Sebelum koreksi tanggal laporan dan anotasi confidence, file graph/laporan terkait dibackup lagi di folder artefak project.','- Tidak ada source aplikasi, database, instalasi dependency, deployment, Git stage/commit/push yang dilakukan oleh sesi ini.','',
        '## File dan sumber yang dibaca','','Memori: E:/AI/Memories/{README.md,INDEX.md,MEMORY.md,20261012-all-projects-index.md}; indeks C dan handoff sinkronisasi 12/15 Oktober, NEWMG, Arunika Map Studio, Codex MultiWorkspace, RPG, JPOG. Script lama ekspor dan Graphify 12/15 Oktober dipakai sebagai acuan, salinannya dipertahankan.','',
        'Source aktif sampel yang diperiksa langsung: Phoenix ArticleTemplateOptions.php dan ManageArticleTemplateController; CMS BUM composer.json dan GradingController.php; Standalone AuthController.php; NEWMG Prebook_captcha.php dan Aruna_S3.php; Arunika Map Studio engine.py; Codex MultiWorkspace manager.cjs/runtime.cjs/menu-model.cjs; AoE 07-defense-communication.per dan helper scenario; RPG navigation.ts/core.ts/App.vue; JPOG Museum.inx/Museum.ini. Ekstraksi lokal membaca corpus source yang lolos filter, tanpa vendor/dependency, backup, artifact, cache, credential, atau salinan historis.','',
        'Query Graphify: ArticleTemplateOptions/HeaderNavigation/Focused Reader; composer/harvest/Gokut; PageBuilder/auth; Prebook_store/captcha/page.tpl/Lead_guard; generate/update_life/ground_cover/bases; sandbox/workspace/manager/login-guard/ensureSharedHistory; gl-under-attack/tribute; build_preview3/scenario; findPath/parseProgress; MaxCapacity/sit_lp. Query dipakai untuk navigasi, kemudian source aktual diperiksa.','',
        '## Batas dan langkah selanjutnya','','- Verifikasi sesi ini adalah integritas arsip dan pemeriksaan source/graph secara statis; tidak menguji aplikasi lewat browser, database produksi, AWS, deployment, atau gameplay.','- Cakupan terbatas pada riwayat yang dapat diakses melalui aplikasi/arsip/profil lokal ini; tidak menjamin chat yang sudah dihapus, perangkat lain, atau seluruh akun ChatGPT web.','- Lampiran biner tidak diekspor; catatan teks dan rujukannya dipertahankan.','- Saat melanjutkan fitur, baca catatan paling baru per chat, verifikasi source/runtime terbaru, dan cek INFERRED/AMBIGUOUS langsung pada source. Graphify berikutnya tetap manual atas perintah pengguna.',''])
    for dest in [E,C]:new(dest/note_name,handoff)
    assert sha(E/note_name)==sha(C/note_name)
    lines=['# Indeks Memori Seluruh Project — 20 Oktober 2026','','Chat ID: '+CURRENT,'','Cakupan: 192 chat terdahulu + chat sinkronisasi ini. Sebanyak 20 catatan baru dan 11 pembaruan ditambahkan, sedangkan 161 catatan chat yang tidak berubah tetap digunakan. Indeks lama dipertahankan.','',
        'Memori utama: E:/AI/Memories. Salinan identik tersedia di C:/Users/aruna/.codex/memories/extensions/ad_hoc/notes.','',
        '## Status sinkronisasi','',graph_table,'',
        '## Chat per project','']
    routed=[]
    for project in sorted({r['project'] for r in rows}):
        lines+=['### '+project,'']
        for row in [r for r in rows if r['project']==project]:
            if row['id'] in by_id:
                name=by_id[row['id']]['name']
            else:
                choices=[Path(p['path']) for p in row['previous']]
                pick=next((p for p in choices if row['id'] in p.name),choices[0])
                name=pick.name
            assert (E/name).is_file() and (C/name).is_file(),name
            assert sha(E/name)==sha(C/name),name
            lines.append('- ['+row['title'].replace('[','(').replace(']',')')+']('+name+') — '+row['action']+'; Chat ID '+row['id'])
            routed.append({'id':row['id'],'project':project,'name':name,'sha256':sha(E/name)})
        lines.append('')
    lines+=['## Chat sinkronisasi ini','','- [Sinkronisasi memori dan Graphify lintas project]('+note_name+')','',
        '## Project tanpa source aktif','',skipped,'','## Batas verifikasi','','Hasil uji aplikasi pada catatan bersifat historis. Sesi ini memverifikasi file memori, source hashes, struktur graph, backup, dan keadaan Git; tidak mengulang uji runtime. Lampiran biner serta seluruh akun/perangkat lain berada di luar cakupan akses ini.','']
    index='\n'.join(lines);index_name='20261020-all-projects-index.md'
    for dest in [E,C]:new(dest/index_name,index)
    assert sha(E/index_name)==sha(C/index_name)
    preserved={p['path']:p['sha256'] for r in rows for p in r['previous']}
    assert all(sha(Path(p))==h for p,h in preserved.items())
    for x in manifest:assert all(sha(dest/x['name'])==x['sha256'] for dest in [E,C])
    assert len(routed)==192 and len({r['id'] for r in routed})==192
    for graph in graphs['results']:assert sha(Path(graph['root'])/'graphify-out/graph.json')==graph['graph_sha256']
    summary={'date':'2026-10-20','memory':{**memory,'current_chat_saved':True,'covered_chats_including_current':193,'new_chat_note_pairs':32,'index_pairs':1,'all_primary_chat_pairs_verified':193,'primary_index_e':str(E/index_name),'primary_index_c':str(C/index_name),'old_memories_still_unchanged':len(preserved)},'graphify':{'graphs_verified':10,'nodes':total_nodes,'edges':total_edges,'confidence':dict(confidence),'same_name_receiver_relations_marked_ambiguous':36,'self_loop_edges_remaining':62,'results':graphs['results'],'skipped':graphs['skipped']},'application_source_modified':False,'runtime_retested':False,'chat_outside_project_artifacts_created':False}
    new(BASE/'final-verification.json',json.dumps(summary,ensure_ascii=False,indent=2))
    new(BASE/'index-routing.json',json.dumps(routed,ensure_ascii=False,indent=2))
    report='\n'.join(['# Hasil Penyimpanan Memori dan Pembaruan Graphify','',
        '20 Oktober 2026 (WIB).','',
        '**Penyimpanan memori selesai dan Graphify 10 lokasi source sudah diperbarui/diverifikasi.**','',
        '- 192 chat terdahulu dicocokkan; 20 catatan baru dan 11 pembaruan disimpan di E/C.','- 161 chat lain memakai arsip identik yang sudah ada. Chat sinkronisasi ini juga disimpan, sehingga cakupan total menjadi 193 chat.','- 32 pasangan catatan baru termasuk chat aktif + 1 pasangan indeks identik SHA-256. Seluruh 193 pasangan catatan utama yang dirujuk indeks tersedia dan identik.','- 395 file memori lama tetap utuh.','',
        'Memori utama: `'+str(E)+'`  \nSalinan C: `'+str(C)+'`  \nIndeks: `'+str(E/index_name)+'`','',
        '## Graphify','',graph_table,'',
        'Total **'+format(total_nodes,',')+' node** dan **'+format(total_edges,',')+' relasi**. Delapan graph lama diproses incremental; RPG dan museum JPOG mendapat graph baru. Dua lokasi tanpa perubahan kode tetap diverifikasi terhadap source.','',
        '## Yang benar-benar diverifikasi','','- Pasangan memori E/C, hash, kelengkapan Chat ID, dan keutuhan memori lama.','- Hash source/manifest, ID node unik, endpoint relasi valid, backup, node yang tidak terdampak, dan HTML tanpa script CDN eksternal.','- HEAD, isi staging, dan status aplikasi tetap sama pada repository yang tersedia.','- Query smoke berhasil untuk source utama, termasuk Map Studio, MultiWorkspace, NEWMG, RPG, dan museum.','',
        '## Peringatan kualitas graph','','Terdapat **4.093 relasi INFERRED** dan **36 relasi AMBIGUOUS**. Anotasi AMBIGUOUS dipakai pada same-name call yang receiver-nya belum terbukti; source aktual harus diperiksa saat diagnosis. Keluaran parser memuat 62 self-loop, termasuk recursion yang valid; seluruhnya dipertahankan, dan 36 yang berpotensi salah target sudah ditandai. Pemeriksaan 14 pasangan arah berlawanan tidak menunjukkan kehilangan relasi karena graph tetap directed.','',
        'JPOG hanya memetakan konfigurasi museum; 14 file lain dicatat sebagai metadata, bukan bukti AST/gameplay. Sebagian file konfigurasi/template pada project lain juga hanya diwakili metadata karena parser tidak menghasilkan simbol.','',
        '## Project dilewati','',skipped,'',
        '## Lokasi bukti dan backup','','Memori per project serta salinan handoff: `<project-root>/project-artifacts/memory-sync-20261020`. Laporan dan backup Graphify: `<project-root>/project-artifacts/graphify-all-projects-20261020`. Graph aktif: `<project-root>/graphify-out/graph.json`.','',
        'Khusus Arunika Map Studio: `D:/Arunika Map Studio/project-artifacts/`. Khusus Codex MultiWorkspace: `D:/Codex-MultiWorkspace/project-artifacts/`. Chat di luar project tidak dibuatkan folder project-artifacts.','',
        'Tidak mengulang pengujian browser/runtime, AWS, database produksi, deployment, atau gameplay. Source aplikasi tidak diubah; tidak ada stage/commit/push. Cakupan arsip dibatasi riwayat lokal/aplikasi yang dapat diakses; lampiran biner dan chat yang terhapus/perangkat lain tidak dijamin tercakup.',''])
    new(OUTPUT/'Laporan_Memori_dan_Graphify_20261020.md',report)
    output_index=re.sub(r'\]\((2026[^)]+\.md)\)',lambda m:'](<'+(E/m[1]).as_posix()+'>)',index)
    new(OUTPUT/'Indeks_Memori_Semua_Project_20261020.md',output_index)
    new(OUTPUT/'Verifikasi_Memori_dan_Graphify_20261020.json',json.dumps(summary,ensure_ascii=False,indent=2))
    # Keep the requested project-local handoff next to each project's verified evidence.
    for graph in graphs['results']:
        root=Path(graph['root']);folder=root/'project-artifacts/memory-sync-20261020'
        status='# Status memori dan Graphify — '+graph['label']+'\n\nTanggal: 20 Oktober 2026 (WIB).\n\nIndeks memori: '+str(E/index_name)+'\n\nGraph: '+str(root/'graphify-out/graph.json')+'\n\n'+json.dumps(graph,ensure_ascii=False,indent=2)+'\n\nHasil ini statis; runtime/gameplay tidak diuji ulang. Source aplikasi dan staging dipertahankan.\n'
        new(folder/'STATUS-MEMORI-GRAPHIFY.md',status)
    print(json.dumps({'covered_chats':193,'note_pairs_written':32,'index_pairs_written':1,'primary_pairs_verified':193,'graphs':10,'nodes':total_nodes,'edges':total_edges,'index_e':str(E/index_name),'outputs':str(OUTPUT)}),flush=True)
if __name__=='__main__':main()
