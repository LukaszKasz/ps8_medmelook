 document.addEventListener("DOMContentLoaded", function () {
  const copyBtn = document.getElementById("copyPromoBtn");
  if (copyBtn) {
    copyBtn.addEventListener("click", function () {
      const code = copyBtn.dataset.code;
      navigator.clipboard.writeText(code)
        .then(() => alert("Skopiowano kod promocyjny: " + code))
        .catch(() => alert("Nie udało się skopiować kodu."));
    });
  }
});

document.addEventListener('DOMContentLoaded', function() {
    const productElement = document.getElementById('product-details');
    if (productElement) {
        const productData = JSON.parse(productElement.getAttribute('data-product'));

        // Szukanie cechy "Rabat" w danych produktu
        const discountFeature = productData.features?.find(
            feature => feature.name === "Rabat" && feature.value
        );

        if (discountFeature) {
            const discountValueMatch = discountFeature.value.match(/(\d+)%/);

            if (discountValueMatch) {
                const discountPercentage = parseInt(discountValueMatch[1], 10);

                const discountFlags = document.querySelectorAll('.product-flag.discount');
                discountFlags.forEach(function(flag) {
                    const flagTextMatch = flag.textContent.trim().match(/-(\d+)%/);
                    if (flagTextMatch && parseInt(flagTextMatch[1], 10) === discountPercentage) {
                        flag.textContent = ""; // Usuń tekst
                        flag.classList.remove('discount'); // Usuń starą klasę 'discount'
                        flag.classList.add('discount-new'); // Dodaj nową klasę 'discount-new'
                        flag.style.backgroundImage = `url('https://medmelook.pl/img/cms/Rabaty/rabat_${discountPercentage}_procent.png')`;
                        flag.style.backgroundSize = 'contain';
                        flag.style.backgroundRepeat = 'no-repeat'; 
                    }
                });
            }
        }

        // Szukanie cechy "Ostatnia sztuka w odcieniu" w danych produktu
        const lastPiece = productData.features?.find(
            feature => feature.name === "Ostatnia sztuka w odcieniu" && feature.value === "Tak"
        );

        if (lastPiece) {
            const lastPieceFlags = document.querySelector('.js-product-flags');
            const lastPieceFlag = document.createElement('li');
            lastPieceFlag.className = 'product-flag last-piece';
            lastPieceFlag.style.backgroundImage = "url('https://medmelook.pl/img/cms/Rabaty/ostatnia_sztuka.png')";
            lastPieceFlag.style.backgroundSize = 'contain';
            lastPieceFlag.style.backgroundRepeat = 'no-repeat'; 
            lastPieceFlags.appendChild(lastPieceFlag);
        }
    }
});
//document.querySelectorAll('.product-flag.discount').forEach(function(flag) {
//    if (flag.textContent.trim() === "-10%") {
//       flag.textContent = ""; // Usuwamy widoczny tekst
//       flag.classList.remove('discount'); // Usuwamy starą klasę
//        flag.classList.add('discount-10'); // Dodajemy nową klasę
//    }
//});
//document.querySelectorAll('.product-flag.discount').forEach(function(flag) {
//    if (flag.textContent.trim() === "-30%") {
//        flag.textContent = ""; // Usuwamy widoczny tekst
//        flag.classList.remove('discount'); // Usuwamy starą klasę
//        flag.classList.add('discount-30'); // Dodajemy nową klasę
//    }
//});

// Znajdź wszystkie inputy typu radio w grupie
// document.querySelectorAll('#group_9 .input-radio').forEach(function(radio) {
//     radio.checked = false; // Odznacz każdą opcję
// });
// 
// Nasłuchujemy na zmianę w grupie 8
// document.querySelectorAll('#group_8 .input-radio').forEach(function(input) {
//     input.addEventListener('change', function() {
//         // Usuń "checked" z wszystkich opcji w grupie 8
//         document.querySelectorAll('#group_9 .input-radio').forEach(function(radio) {
//             radio.checked = false;
//         });
//     });
// });

// const button = document.querySelector('.btn.add-to-cart');
// const newButton = button.cloneNode(true); // Tworzy kopię przycisku
// button.replaceWith(newButton); // Usuwa wszystkie powiązane zdarzenia
// newButton.addEventListener('click', function(event) {
//     const isSizeSelected = document.querySelector('#group_9 .input-radio:checked');
//     if (!isSizeSelected) {
//         event.preventDefault();
//          alert('Proszę wybrać rozmiar przed dodaniem do koszyka.');
//     }
// });

/* 

Nadpisanie klasy
document.querySelectorAll('.product-flag.discount').forEach(function(flag) {
    if (flag.textContent.trim() === "-7%") {
        flag.className = 'product-flag discount-7'; // Zastąp wszystkie klasy nową wartością
    }
});


Usunięcie i dodanie klasy
document.querySelectorAll('.product-flag.discount').forEach(function(flag) {
    if (flag.textContent.trim() === "-7%") {
        flag.textContent = ""; // Usuwamy widoczny tekst
        flag.classList.remove('discount'); // Usuwamy starą klasę
        flag.classList.add('discount-7'); // Dodajemy nową klasę
    }
});


Dodanie klasy
document.querySelectorAll('.product-flag.discount').forEach(function(flag) {
    if (flag.textContent.trim() === "-7%") {
        flag.textContent = ""; // Usuń tekst
        flag.classList.add('discount-7'); // Dodaj klasę dla -7%, jeśli potrzebne
    }
});

Kolonowane klasy
document.querySelectorAll('.product-flag.discount').forEach(function(flag) {
    if (flag.textContent.trim() === "-7%") {
        const newFlag = flag.cloneNode(true); // Skopiuj istniejący element
        newFlag.classList.remove('discount'); // Usuń klasę `discount` z nowej kopii
        newFlag.classList.add('discount-7'); // Dodaj klasę `discount-7` do nowej kopii
        flag.parentElement.insertBefore(newFlag, flag.nextSibling); // Dodaj nowy element po oryginalnym
    }
});

*/
