// on récupère toutes les catégories
const tabs = document.querySelectorAll(".menu-tab");
 
// on boucle sur les catégporie
for(let tab of tabs)
{
  //  on ajourte un event au click 
    tab.addEventListener("click", function()
    {
      // tabId recup  l'id de la catégorie
      let tabId = tab.dataset.tab;
      // on récupère tout les btn avec class .benefit-btn
      let benefits = document.querySelectorAll(".benefit-btn");
      // fonction qui cache tout les btn benefit-btn
      clearActiveTab();

      // on ajoute la class active sur la catégorie peut etre utile pour le css ou autre
      tab.classList.add("active");
      // pour chaque benefit
      for(let benefit of benefits)
      {
        // si les data recuperer du  benefit == a la catégorie qu'on a cliqué 
          if(benefit.dataset.category == tabId)
          {
            // on ajoute la class active a ces benefit 
            benefit.classList.add("active");
          }
        }
    })
}
//   fonction qui masque tout les benefits
function clearActiveTab()
{
  // pour chaque catégories 
    for(let tab of tabs)
    {
      let benefits = document.querySelectorAll(".benefit-btn");
      tab.classList.remove("active");

        for(let benefit of benefits)
        {
            benefit.classList.remove("active");
        }
    }
}

// on récupère toutes les catégories

let togg1 = document.getElementById("togg1");
let togg2 = document.getElementById("togg2");
let d1 = document.getElementById("d1");
let d2 = document.getElementById("d2");
togg1.addEventListener("click", () => {
  if(getComputedStyle(d1).display != "none"){
    d1.style.display = "none";
  } else {
    d1.style.display = "block";
  }
})

