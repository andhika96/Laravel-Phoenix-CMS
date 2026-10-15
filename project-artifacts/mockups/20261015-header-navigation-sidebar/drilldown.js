(()=>{
const root=document.getElementById('headerNavigationEditor');
root.querySelector('.sidebar-subtitle').textContent='Pilih bagian yang ingin disesuaikan.';
const content=root.querySelector('.inspector-content');
const directory=document.createElement('div');directory.className='setting-directory';
const heading=document.createElement('div');heading.className='detail-heading';heading.hidden=true;
const back=document.createElement('button');back.type='button';back.textContent='←';back.setAttribute('aria-label','Kembali ke semua pengaturan');
const title=document.createElement('span');heading.append(back,title);content.prepend(heading);content.prepend(directory);
const entries=[['Tampilan','Posisi & susunan','Logo, menu, dan lebar header','Layout','layout','▥'],['','Ukuran & jarak','Tinggi, padding, radius, dan margin','Sizing','layout','↔'],['','Warna & state','Header, link, hover, dan focus','Colors','style','◐'],['','Bayangan link','Offset, blur, warna, dan inset','Effects','style','◒'],['Interaksi','Perilaku scroll','Fixed, sticky, dan transparansi','Behavior','advanced','↕'],['Lanjutan','Konfigurasi','Lihat konfigurasi header','Config Preview','advanced','{ }']];
let lastButton;
for(const [group,label,description,category,panel,symbol] of entries){
if(group){const caption=document.createElement('div');caption.className='directory-label';caption.textContent=group;directory.append(caption);}
const button=document.createElement('button');button.type='button';button.className='directory-item';button.dataset.detail=category;
const icon=document.createElement('span');icon.className='directory-symbol';icon.textContent=symbol;icon.setAttribute('aria-hidden','true');const copy=document.createElement('span');const strong=document.createElement('strong');strong.textContent=label;const small=document.createElement('small');small.textContent=description;copy.append(strong,small);const arrow=document.createElement('span');arrow.textContent='›';arrow.setAttribute('aria-hidden','true');button.append(icon,copy,arrow);directory.append(button);
button.addEventListener('click',()=>{root.querySelector('[data-inspector-tab="'+panel+'"]').click();root.querySelector('[data-settings-category="'+category+'"]').click();directory.hidden=true;heading.hidden=false;title.textContent=label;lastButton=button;back.focus();});
}
function home(){root.querySelectorAll('[data-inspector-panel]').forEach(panel=>panel.hidden=true);directory.hidden=false;heading.hidden=true;lastButton?.focus();}
back.addEventListener('click',home);home();
})();
