/* 
 * original dropzone design by pinceladasdaweb (https://github.com/pinceladasdaweb/imgur-upload),
 * trimmed down massively to only support one dropzone and one file, and without imgur uploading
 */

var callback = null;

function setDropzoneCallback(cb) {
    callback = cb;
}

function registerZoneListeners(zone) {
    var events = ['dragenter', 'dragleave', 'dragover', 'drop'], file, target, i, len;

    zone.addEventListener('change', function (e) {
        if (e.target && e.target.nodeName === 'INPUT' && e.target.type === 'file') {
            let files = e.target.files;
            if (callback && files.length > 0) {
                callback(files[0]);  // single file only
            }
        }
    }.bind(this), false);

    events.map(function (event) {
        zone.addEventListener(event, function (e) {
            if (e.target && e.target.nodeName === 'INPUT' && e.target.type === 'file') {
                if (event === 'dragleave' || event === 'drop') {
                    e.target.parentNode.classList.remove('dropzone-dragging');
                } else {
                    e.target.parentNode.classList.add('dropzone-dragging');
                }
            }
        }, false);
    });
}

function createEls(name, props, text) {
    var el = document.createElement(name), p;
    for (p in props) {
        if (props.hasOwnProperty(p)) {
            el[p] = props[p];
        }
    }
    if (text) {
        el.appendChild(document.createTextNode(text));
    }
    return el;
}

function insertAfter(referenceNode, newNode) {
    referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
}
        
var zone = document.querySelector('.dropzone')

zone.appendChild(createEls('p', {}, 'Drag, paste or select your image.'));
zone.appendChild(createEls('input', {type: 'file', multiple: 'multiple', accept: 'image/*'}));
//this.insertAfter(zone, createEls('div', {className: 'status'}))

registerZoneListeners(zone);

document.onpaste = function(event) {
    var items = (event.clipboardData || event.originalEvent.clipboardData).items;
    for (var index in items) {
        var item = items[index];
        if (item.kind === 'file') {
            if (callback)
                callback(item.getAsFile());
            break;  // single file only
        }
    }
}
    