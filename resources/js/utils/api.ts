const defaultHeaders = {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
};

export const api = {
    get: (url: string) => fetch(url, {
        headers: defaultHeaders
    }),

    post: (url: string, data: any) => fetch(url, {
        method: 'POST',
        // headers: defaultHeaders,
        body: data
    }),

    put: (url: string, data: any) => fetch(url, {
        method: 'PUT',
        headers: defaultHeaders,
        body: JSON.stringify(data)
    }),

    delete: (url: string) => fetch(url, {
        method: 'DELETE',
        headers: defaultHeaders
    })
};
