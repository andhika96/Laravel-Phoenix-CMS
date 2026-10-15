(()=>{
const root=document.getElementById('headerNavigationEditor');
root.setAttribute('aria-label','Usulan desain sidebar Header Navigation');
root.querySelector('.pn-inspectorhead strong').textContent='Header navigation';
const subtitle=document.createElement('span');subtitle.className='sidebar-subtitle';subtitle.textContent='Pengaturan tampilan & interaksi';root.querySelector('.pn-inspectorhead').append(subtitle);
const device=document.createElement('label');device.className='sidebar-device';device.textContent='Sedang mengatur';device.append(root.querySelector('#deviceMode'));root.querySelector('.pn-tabs').after(device);
const labels={Preview:'Perangkat',Layout:'Posisi',Sizing:'Ukuran',Colors:'Warna',Effects:'Bayangan',Behavior:'Perilaku','Config Preview':'Konfigurasi'};
root.querySelectorAll('[data-settings-category]').forEach(button=>{button.textContent=labels[button.dataset.settingsCategory];if(button.dataset.settingsCategory==='Preview')button.hidden=true;});
const stateRows=[...root.querySelectorAll('[data-link-color-state]')];
const switcher=document.createElement('div');switcher.className='state-switch';switcher.setAttribute('role','group');switcher.setAttribute('aria-label','State warna link');
for(const state of ['active','hover','focus']){const button=document.createElement('button');button.type='button';button.textContent=state.charAt(0).toUpperCase()+state.slice(1);button.dataset.colorState=state;button.addEventListener('click',()=>{stateRows.forEach(row=>row.hidden=row.dataset.linkColorState!==state);switcher.querySelectorAll('button').forEach(b=>b.setAttribute('aria-pressed',String(b===button)));});switcher.append(button);}
root.querySelector('.link-color-state-list').before(switcher);switcher.querySelector('button').click();
for(const row of stateRows){const labels=row.querySelectorAll('label');if(labels[0])labels[0].textContent='Warna';if(labels[1])labels[1].textContent='Border';}
const hints={layout:'Ukuran responsif mengikuti perangkat yang dipilih di atas.',style:'Warna teks pada state link menyesuaikan kontras secara otomatis.',advanced:'Fixed atau Sticky mengaktifkan pilihan animasi scroll.'};
for(const panel of root.querySelectorAll('[data-inspector-panel]')){const help=document.createElement('p');help.className='sidebar-help';help.textContent=hints[panel.dataset.inspectorPanel];panel.append(help);}
root.querySelector('[data-settings-category="Layout"]').click();
if(globalThis.Tweak){const design={accent:'#2563eb'};const tweak=new Tweak({container:root,onChange:()=>root.style.setProperty('--pn-accent',design.accent)});tweak.addColorPicker(design,'accent',{label:'Warna aksen',reference:'--pn-accent'});}
})();
