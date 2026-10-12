const stories=[...document.querySelectorAll('article')];
const empty=document.createElement('p');empty.className='preview-empty';empty.textContent='No matching stories. Try another keyword or category.';empty.hidden=true;
document.querySelector('[data-article-vue-list-content]').append(empty);
function filterStories(){
  const term=document.querySelector('[name=search]').value.trim().toLowerCase(),select=document.querySelector('[name=category]'),category=select.value?select.selectedOptions[0].textContent.trim():'';
  let count=0;
  stories.forEach(story=>{const title=story.querySelector('.article-title-clamp').textContent,excerpt=story.querySelector('.article-excerpt-clamp')?.textContent||'',label=story.querySelector('.article-chip')?.textContent.trim()||'';story.hidden=!(`${title} ${excerpt}`.toLowerCase().includes(term)&&(!category||label===category));if(!story.hidden)count++;});
  document.querySelectorAll('.article-mosaic-grid,.article-classic-sidebar,.article-classic-highlights,.article-classic-grid').forEach(group=>group.hidden=![...group.querySelectorAll('article')].some(story=>!story.hidden));
  const layout=document.querySelector('.article-classic-layout');if(layout)layout.style.gridTemplateColumns=layout.querySelector('.article-classic-lead').hidden?'1fr':'';
  empty.hidden=count>0;document.querySelector('.article-pagination').hidden=!!term||!!category;
}
document.querySelector('[data-article-filter]').addEventListener('submit',event=>{event.preventDefault();filterStories();});
document.querySelector('[name=category]').addEventListener('change',filterStories);
document.addEventListener('click',event=>{const link=event.target.closest('a');if(!link)return;event.preventDefault();const story=link.closest('article');if(story){document.getElementById('dialog-title').textContent=story.querySelector('.article-title-clamp').textContent.trim();document.getElementById('dialog-copy').textContent=story.querySelector('.article-excerpt-clamp')?.textContent.trim()||'Sample editorial story.';document.getElementById('article-dialog').showModal();}});
