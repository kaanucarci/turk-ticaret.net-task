$(document).ready(function() {
   function apiRequest(endpoint, method, data, headers, functionName)
   {
       const baseUrl = 'http://127.0.0.1:8000/api/';

           fetch(baseUrl + endpoint, {
               method: method,
               headers: headers,
               body: data,
           })
               .then(response => response.json())
               .then(data => {
                   if (typeof window[functionName] === 'function') {
                       window[functionName](data); // functionName adındaki fonksiyonu çağır
                   } else {
                       console.error(`Fonksiyon ${functionName} bulunamadı.`);
                   }
               });
   }

   $(document).on('submit', function (event){
      event.preventDefault();
      const form = $(event.target);
      const dataArray = form.serializeArray();
      const data = {};

       dataArray.forEach(item => {
           data[item.name] = item.value;
       });

      const headers = {
          'Content-Type': 'application/json'
      };
      const endpoint = form.attr('action');
      const method = form.attr('method');
      const functionName = form.attr('data-function');

      apiRequest(endpoint, method, JSON.stringify(data), headers, functionName);
   });


});

function Login(data)
{
    if (data.error)
    {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Kullanıcı Adı veya Parola yanlış',
            confirmButtonText: 'Tamam'
        });
    }
    else
    {
        localStorage.setItem("access_token", data.access_token);
        window.location.href = window.location.origin;
    }

}

