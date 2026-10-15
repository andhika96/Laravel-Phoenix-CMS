// Keep the CMS control bindings; change only disclosure and preview presentation.
const previewToolbar=editor.querySelector('.pn-previewbar');
const conditionLabel=document.createElement('label');conditionLabel.className='condition-control';conditionLabel.textContent='Kondisi';
const condition=document.createElement('select');condition.id='previewCondition';condition.setAttribute('aria-label','Kondisi preview');condition.innerHTML='<option value="0">Awal halaman</option><option value="50">Setelah scroll</option>';conditionLabel.append(condition);previewToolbar.append(conditionLabel);
condition.addEventListener('change',()=>{controls.scrollSim.value=condition.value;updateAll();els.deviceStage.scrollTop=(Number(condition.value)/100)*previewScrollMax();});
els.deviceStage.addEventListener('scroll',()=>{condition.value=Number(controls.scrollSim.value)>8?'50':'0';},{passive:true});
const originalSync=syncPrototype;
syncPrototype=function(){originalSync();condition.disabled=!scrollEffectsEnabled();condition.value=Number(controls.scrollSim.value)>8?'50':'0';condition.title=scrollEffectsEnabled()?'Uji tampilan sebelum dan setelah scroll':'Aktifkan Fixed/Sticky dan animasi pada Advanced';};
for(const panel of editor.querySelectorAll('[data-inspector-panel]')){
const sections=[...panel.querySelectorAll('details')];
const container=panel.querySelector('.header-settings-accordion');
const navigation=document.createElement('nav');navigation.className='settings-category-nav';navigation.setAttribute('aria-label','Kelompok '+panel.dataset.inspectorPanel);
const fields=document.createElement('div');fields.className='settings-category-fields';
container.append(navigation,fields);
const selectSection=section=>{sections.forEach((other,index)=>{other.hidden=other!==section;other.open=other===section;navigation.children[index].setAttribute('aria-pressed',String(other===section));});};
sections.forEach(section=>{const title=section.querySelector('.header-setting-summary-title').textContent.trim();const button=document.createElement('button');button.type='button';button.textContent=title;button.dataset.settingsCategory=title;button.addEventListener('click',()=>selectSection(section));navigation.append(button);fields.append(section);});
selectSection(sections.find(section=>section.open)||sections[0]);
}
const showAll=document.createElement('button');showAll.type='button';showAll.className='show-all-settings';showAll.textContent='Tampilkan semua opsi';showAll.setAttribute('aria-pressed','false');editor.querySelector('.pn-inspectorhead').append(showAll);
showAll.addEventListener('click',()=>{const expanded=showAll.getAttribute('aria-pressed')!=='true';showAll.setAttribute('aria-pressed',String(expanded));showAll.textContent=expanded?'Kembali per kategori':'Tampilkan semua opsi';editor.querySelector('.pn-inspector').classList.toggle('all-settings',expanded);editor.querySelectorAll('[data-inspector-panel]').forEach(panel=>{const sections=[...panel.querySelectorAll('details')];const buttons=panel.querySelectorAll('[data-settings-category]');sections.forEach((section,index)=>{section.hidden=!expanded&&buttons[index].getAttribute('aria-pressed')!=='true';section.open=!section.hidden;});});});
editor.querySelector('.pn-caption').textContent='Preview website';
editor.querySelector('.pn-titlebar h1').textContent='Header navigation';
editor.querySelector('.pn-titlebar p').textContent='Rancang navigasi yang terasa menyatu dengan website Anda.';
editor.querySelector('.pn-inspectorhead strong').textContent='Sesuaikan header';
editor.querySelector('.pn-badge').textContent='';
editor.querySelector('.hero-copy').innerHTML='<span>PHOENIX · WEBSITE PREVIEW</span><h2>Ruang untuk<br>ide yang lebih besar.</h2><p>Header yang tepat membantu pengunjung menemukan apa yang mereka butuhkan.</p>';
editor.querySelector('.page-band').innerHTML='<h3>Dirancang untuk terhubung.</h3><p>Konten halaman contoh untuk melihat proporsi navigasi Anda.</p>';
syncPrototype();
