
function preventContext(e) 
{
    e.preventDefault(); 
    e.stopPropagation();   
}

window.addEventListener('DOMContentLoaded', async (event) => {
    console.error("DOM LOADED");
     document.getElementById("em").addEventListener('contextmenu', preventContext, false);
     window.addEventListener('contextmenu', preventContext, false);
     document.body.addEventListener("contextmenu",preventContext,false);
      let b64Data = document.getElementById("data").innerText;
      var blob = await convertDataURIToBinaryFetch(b64Data);
      let url = URL.createObjectURL(blob) + '#toolbar=0&navpanes=0&scrollbar=0&view=Fit';
      let elem = document.createElement("embed");
      elem.id = "em";
      elem.height = "100%";
      let em = document.getElementById('em');
      elem.setAttribute("src",url);
      em.parentElement.replaceChild(elem,em);
});

function b64toBlob(b64Data, contentType) {
    var byteCharacters = atob(b64Data);
    
    var byteArrays = [];
    
    for (let offset = 0; offset < byteCharacters.length; offset += 512) {
        var slice = byteCharacters.slice(offset, offset + 512),
            byteNumbers = new Array(slice.length);
        for (let i = 0; i < slice.length; i++) {
            byteNumbers[i] = slice.charCodeAt(i);
        }
        var byteArray = new Uint8Array(byteNumbers);
    
        byteArrays.push(byteArray);
    }
    
    var blob = new Blob(byteArrays, { type: contentType });
    return blob;
}
function convertDataURIToBinaryFetch(dataURI) {
    return fetch(dataURI)
      .then((res) => res.blob());
  }