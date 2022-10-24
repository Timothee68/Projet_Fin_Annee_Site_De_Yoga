// window.onload = () => {
//     const FiltersForm = document.querySelector("#filters");
//     // on boucle sur les inputs
//     document.querySelectorAll("#filters input").forEach(input => {
//         input.addEventListener("change", () => {

//             // on intercepte les clics
//             // on récupère les données du formulaires
//             const Form = new FormData(FiltersForm);
//             // on fabrique la 'querryString'
//             const Params = new URLSearchParams();

//             Form.forEach((value,key) => {
//                 Params.append(key,value);
//             // console.log(Params.toString());  
//              // on récupère l'url active
//              const Url = new URL(window.location.href);
//             //  console.log(Url);
//             // on lance la requete ajax 
           
//             fetch(Url.pathname + "?" + Params.toString() + "&ajax=1",{   
//                 headers: {
//                     "X-Requested-With": "XMLHttpRequest"
//                 } 
//             }).then(response => {
//                 console.log(response);
//                response => response.json()
//             })
//             // .then(data => {
//             //     //on va cherchez la zone de contenue
//             //     const content = document.querySelector("#content");
//             //     // on remplace le contenue
//             //     content.innerHTML = data.content;
//             //     // on met a jour l'url 
//             //     history.pushState({}, null, Url.pathname+ "?" +Params.toString() )
//             // })
//             .catch(e => alert(e));

//             })
//         } )
//     })

// }

