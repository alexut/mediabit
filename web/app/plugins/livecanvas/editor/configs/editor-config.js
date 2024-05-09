//DEFINE EDITABLE ELEMENTS SETTINGS OBJECT 
const theEditorConfig = {
    editable_elements: {
        "image": { //this is the panel name
            selector: "img", //CSS selector
        },
        "icon": {
            selector: "i.fa",
        },
        "svg-icon": {
            selector: "svg",
        },
        "button": {
            selector: "button, a.btn",
        },
        "background": {
            selector: "[lc-helper=background]", //we shouldnt refer to lc-helper if possible for purity, but still can be used
        },
        "video-embed": { //generic iframe / video embed panel. This panel needs to be called on the parent
            selector:   "[lc-helper=video-embed]", // ".ratio:has(iframe)"
        },
        "gmap-embed": {
            selector: "[lc-helper=gmap-embed]",
        },
        "video-bg": {
            selector: "[lc-helper=video-bg]",
        },
        "shortcode": {
            selector: "[lc-helper=shortcode]",
        },
        "posts-loop": {
            selector: "[lc-helper=posts-loop]",
        },
        
        /* "carousel": {
            selector: ".carousel",
        }, */
    },
};
