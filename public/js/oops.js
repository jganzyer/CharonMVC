function load(fn)
{
  window.addEventListener("load", fn);
}
function selector(selector)
{
  return document.querySelector(selector);
}
function click(elem, fn)
{
  [].forEach.call(document.querySelectorAll(elem), function(el) {
    el.addEventListener('click', function(e) {
      fn.call(null,el,e);
    }, false);
  });
}
function find(elem,child)
{
  return elem.querySelector(child);
}
function each(selector, fn)
{
  [].forEach.call(document.querySelectorAll(selector), function(el) {
    fn.call(null, el);
  });
}
function addClass(elem, name)
{
  elem.classList.add(name);
}
function removeClass(elem, name)
{
  elem.classList.remove(name);
}
function attr(elem, name, value)
{
  if (typeof value === "undefined")
  {
    return elem.getAttribute(name);
  }
  return elem.setAttribute(name, value);
}
function parent(elem)
{
  return elem.parentNode;
}
function css(elem, name, value)
{
  if (typeof value === "undefined")
  {
    return getComputedStyle(elem)[name];
  }
  return elem.style[name] = value;
}
function html(elem, value)
{
  if (typeof value === "undefined")
  {
    return elem.innerHTML;
  }
  return elem.innerHTML = value;
}
function find(elem, selector)
{
  return elem.querySelector(selector);
}
load(function(){
  click(".tab", function(el){
    each(".tab", function(elem){
      removeClass(elem,"active");
    });
    addClass(el,"active");
    var content = selector("[data-content-tab='" + attr(el, "data-tab") + "']");
    each(".content-tab", function(elem){
      removeClass(elem, "active");
    });
    addClass(content,"active");
  });
  click(".button-back", function(){
    history.go(-1);
  });
  click(".error", function(el,e){
    each(".error", function(elem){
      removeClass(elem, "active");
    });
    addClass(el, "active");
    html(selector(".right-code"), html(find(el, ".error-code")));
    html(selector(".right-detail"), html(find(el, ".error-detail")));
    html(selector(".right-file"), '<span class="highlight">File:</span>' + html(find(el, ".error-file")));
  });
  each('.code-error', function(el){
    var p = parent(el), elclass = attr(el, "class"), color;
    color = css(selector(".error." + elclass.replace("code-error " , "") + " .error-icon"), "background-color");
    css(p, 'color', color);
  });
});
