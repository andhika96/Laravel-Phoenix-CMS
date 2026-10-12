const createPrototypeApp=Vue.createApp;
Vue.createApp=function(config,...args){
  const originalData=config.data,originalMounted=config.mounted;
  config.data=function(){return {...originalData.call(this),prototypeRevamp:true,responseStatusToast:'ph-callout-success',isArrayMessageAfterSubmit:0,responseMessageAfterSubmit:''};};
  config.methods.prototypeAppearance=function(value){this.prototypeRevamp=value;document.getElementById('revamp-css').disabled=!value;this.$nextTick(()=>this.fitOptionsPreview());};
  config.methods.prototypeTemplate=function(value){const [surface,key]=value.split(':');this.optionsModal.dirty=false;this.modalPreviewUrl='';this.surface=surface;this.draft[surface==='archive'?'archive_template':'detail_template']=key;this.$nextTick(()=>this.openTemplateOptions());};
  config.methods.prototypeNotify=function(status){if(!status)return;this.responseStatusToast='ph-callout-'+(status==='failed'?'danger':status);this.isArrayMessageAfterSubmit=status==='failed'?1:0;this.responseMessageAfterSubmit=status==='success'?'Perubahan template berhasil diterapkan pada draft.':status==='warning'?'Masih ada perubahan yang belum diterapkan.':[['Preview tidak dapat dimuat. Coba lagi.']];this.$nextTick(()=>{const toast=document.querySelector('.ph-notice-toast');bootstrap.Toast.getOrCreateInstance(toast,{autohide:false}).show();});};
  const applyPrototypeDraft=config.methods.applyTemplateOptions;
  config.methods.applyTemplateOptions=function(){applyPrototypeDraft.call(this);this.prototypeNotify('success');};
  config.mounted=function(){originalMounted?.call(this);window.prototypeVm=this;this.$nextTick(()=>this.openTemplateOptions());};
  const app=createPrototypeApp.call(Vue,config,...args);
  app.config.globalProperties.t=value=>String(value);
  return app;
};
