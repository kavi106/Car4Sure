
export async function callApi(method, endpoint, Data = {}, accessToken = "", AddHeaders = [], Type='json') {
    const headers = new Headers();
    if (accessToken !== "") {
        const bearer = `Bearer ${accessToken}`;
        headers.append("Authorization", bearer);
    }
    AddHeaders.forEach((element) => headers.append(element["name"], element["value"]));

    const options = {
        method: method,
        headers: headers,
    };

    if (Object.keys(Data).length > 0) {
        if (Type == 'json') options["body"] = JSON.stringify(Data);
        if (Type == 'urlencoded') options["body"] = new URLSearchParams(Data);
    }

    return fetch(endpoint, options)
        .then(response => response.json())
        .catch(error => console.log(error))
}