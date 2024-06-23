const back_arrow = url => {
    htmx
        .ajax('GET', url, { target: '#content', swap: 'transition:true' })
        .then(_ => history.pushState({}, '', url))
}
