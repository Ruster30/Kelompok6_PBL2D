document.addEventListener("DOMContentLoaded",function(){setTimeout(function(){document.querySelectorAll(".skeleton-init").forEach(function(e){e.classList.remove("skeleton-init")})},400)});

// Auto-add data-label attributes to mobile tables
document.addEventListener("DOMContentLoaded",function(){
document.querySelectorAll(".card-view-mobile").forEach(function(table){
var headers=[];
table.querySelectorAll("thead th").forEach(function(th){
headers.push(th.textContent.trim())
});
table.querySelectorAll("tbody tr").forEach(function(tr){
tr.querySelectorAll("td").forEach(function(td,i){
if(!td.hasAttribute("data-label")&&headers[i]){
td.setAttribute("data-label",headers[i])
}
})
})
})
});