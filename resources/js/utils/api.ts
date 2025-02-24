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
        body: data
    }),

    put: (url: string, data: any) => fetch(url, {
        method: 'PUT',
        headers: {
            'Accept': 'application/json',
        },
        body: data
    }),

    delete: (url: string) => fetch(url, {
        method: 'DELETE',
        headers: defaultHeaders
    })
};
