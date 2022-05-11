(function( $ ) {
	'use strict';
  $(function() {
  });
	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

})( jQuery );

function setCookie(name,value,days) {
  var expires = "";
  if (days) {
      var date = new Date();
      date.setTime(date.getTime() + (days*24*60*60*1000));
      expires = "; expires=" + date.toUTCString();
  }
  document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}
function getCookie(name) {
  var nameEQ = name + "=";
  var ca = document.cookie.split(';');
  for(var i=0;i < ca.length;i++) {
      var c = ca[i];
      while (c.charAt(0)==' ') c = c.substring(1,c.length);
      if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
  }
  return null;
}
function eraseCookie(name) {   
  document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}

class wplgVideo {
    constructor ( wplgvideo ) {
      this.wplgvideo = wplgvideo
      this.wplgvideoCon = wplgvideo.parentNode
      this.id = wplgvideo.getAttribute('id')
      this.media = wplgvideo.querySelector('video')
      this.controls = wplgvideo.querySelector('.controls')
      this.overlay = wplgvideo.querySelector('.overlay')
      this.play = wplgvideo.querySelector('.play');
      this.rwd = wplgvideo.querySelector('.rwd');
      this.fwd = wplgvideo.querySelector('.fwd');
      this.fullScreenToggle = wplgvideo.querySelector('.fullscreen-toggle');
      this.timerWrapper = wplgvideo.querySelector('.timer');
      this.timer = this.timerWrapper.querySelector('.timer span');
      this.timerBar = this.timerWrapper.querySelector('.timer div');
      this.intervalFwd;
      this.intervalRwd;
      this.actions = []
      this.actionsFinished = []
      this.initwplgvideo()      
    }
       
    initwplgvideo() {
        this.media.removeAttribute('controls');
        this.controls.toggleAttribute('active');
        // this.wplgvideo.addEventListener('contextmenu', event => {
        //   event.preventDefault()
        // })
        this.getActions()
        .then((response)=>{
          this.fillActions(JSON.parse(response))
          this.play.addEventListener('click', this.playHandler.bind(this));
          this.media.addEventListener('ended', this.stopMedia.bind(this));
          this.rwd.addEventListener('click', this.mediaBackward.bind(this));
          this.fwd.addEventListener('click', this.mediaForward.bind(this));
          this.fullScreenToggle.addEventListener('click', this.toggleFullScreen.bind(this));
          this.media.addEventListener('timeupdate', this.updateTime.bind(this));
          this.media.addEventListener('timeupdate', this.checkForActions.bind(this));         
          this.timerWrapper.addEventListener('click', this.setTimer.bind(this));         
        })
        .catch( error => {
          console.error(error)
        })
    }
    
    decodeEntity(inputStr) {
      var textarea = document.createElement("textarea");
      textarea.innerHTML = inputStr;
      return textarea.value;
    }
    
    fillActions(data){
      data.forEach(element => {
        fetch( wplg_params.ajax_url, {
          method: 'post',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({
            action: 'wplg_get_form',
            form_id: element.FormID,
          })          
        })
        .then( res => res.json() )
        .then( data => {
          if (data) {
              this.actions.push({
                "id" : element.FormID,
                "time" : element.TimeMarker,
                "type" : "form",
                "html" : data.html,
                "submit_txt": data.submit_txt
              })
            }
        })
        .catch( error => console.log(error) );        
      })
    }
        
    async getActions() {
      return await new Promise(resolve => {
        fetch( wplg_params.ajax_url, {
          method: 'post',
          body: new URLSearchParams({
            action: 'get_actions',
            wplg_id: this.id,
          })          
        }).then(function (response) {
          resolve(response.text())
        })
      })
    }
  
    playVideo() {
      this.media.play();
    }
    
    stopVideo() {    
      this.media.pause()
    }
  
    playHandler() {
      if (this.controls.hasAttribute('active')) {
        this.play.toggleAttribute('pause')
        this.playPauseMedia()
      }
    }

    playPauseMedia() {    
      this.rwd.classList.remove('active')
      this.fwd.classList.remove('active')
      if(this.media.paused) {
        this.playVideo()
      } else {
        this.stopVideo()
      }
    }
  
    stopMedia() {
      this.rwd.classList.remove('active');
      this.fwd.classList.remove('active');
      this.stopVideo();
      this.media.currentTime = 0;
    }
  
    mediaBackward() {
      if (this.controls.hasAttribute('active')) {
        this.media.currentTime -= 3;
      }
    }
  
    mediaForward() {
      if (this.controls.hasAttribute('active')) {
        this.media.currentTime += 3;
      }
    }    

    setFullScreen(switcher = "on") {
      if ( switcher == "on" ) {
        this.wplgvideo.classList.add('fullscreen')
      } else {
        this.wplgvideo.classList.remove('fullscreen')
      }
      let nodeAfterBody = document.body.firstChild
      if (this.wplgvideo.classList.contains('fullscreen')) {
        nodeAfterBody.parentNode.insertBefore(this.wplgvideo, nodeAfterBody)
      } else {
        this.wplgvideoCon.append(this.wplgvideo)
      }
    }

    toggleFullScreen() {
      this.wplgvideo.classList.toggle('fullscreen')
      let nodeAfterBody = document.body.firstChild
      if (this.wplgvideo.classList.contains('fullscreen')) {
        nodeAfterBody.parentNode.insertBefore(this.wplgvideo, nodeAfterBody)
      } else {
        this.wplgvideoCon.append(this.wplgvideo)
      }
    }
  
    computerToHumanTime(computerTime) {
      let minutes = Math.floor(computerTime / 60);
      let seconds = Math.floor(computerTime - minutes * 60);
      let minuteValue;
      let secondValue;  
      if (minutes < 10) {
        minuteValue = '0' + minutes;
      } else {
        minuteValue = minutes;
      }  
      if (seconds < 10) {
        secondValue = '0' + seconds;
      } else {
        secondValue = seconds;
      }  
      return [ String(minuteValue), String(secondValue) ];
    }

    setTimer(event) {
      if (this.controls.hasAttribute('active')) {
        let x = event.pageX - this.timerWrapper.getBoundingClientRect().left
        let percentage = (x*100)/this.timerWrapper.offsetWidth
        let duration = this.media.duration
        let time = (duration*percentage)/100
        this.setTime(time)      
      }
    }
  
    humanToComputer(humanTime) {
      let time = humanTime.split(':');
      return Number(time[0]) * 60 + Number(time[1]);
    }
  
    setTime(time) {
      this.media.currentTime = time;
    }

    updateTime() {
      let humanTime = this.computerToHumanTime(this.media.currentTime);
      let mediaTime = humanTime[0] + ':' + humanTime[1];
  
      this.timer.textContent = mediaTime;
  
      let barLength = this.timerWrapper.clientWidth * (this.media.currentTime/this.media.duration);
      this.timerBar.style.width = barLength + 'px';  
    }
  
    getFormData(form){
      var unindexed_array = form.serializeArray();
      var indexed_array = {};
  
      form.map(unindexed_array, function(n, i){
        indexed_array[n['name']] = n['value'];
      });

      return indexed_array;
    }

    paramsToObject(entries) {
      const result = {}
      for(const [key, value] of entries) { // each 'entry' is a [key, value] tupple
        result[key] = value;
      }
      return result;
    }

    submitForm(event) {
      event.preventDefault()
      // const formData = Object.fromEntries(new FormData(event.target).entries())      
      let formData = {}
      event.target.querySelectorAll("input").forEach(element => {
        if (element.getAttribute("type") == "tel") {
          let iti = window.intlTelInputGlobals.getInstance(element)
          let prefix = iti.getSelectedCountryData().dialCode
          formData[element.getAttribute("name")] = prefix + element.value
          
        } else {
          formData[element.getAttribute("name")] = element.value
        }
      });

      const queryString = window.location.search
      const urlParams = new URLSearchParams(queryString)
      const urlEntries = urlParams.entries()
      const urlObject = this.paramsToObject(urlEntries)
      const formID = event.target.parentNode.getAttribute('form_id')

      const data = {
        ...formData,
        ...urlObject
      }

      new Promise(resolve => {
        fetch( wplg_params.ajax_url, {
          method: 'post',
          body: new URLSearchParams({
            action: 'wplg_save_lead',
            data: JSON.stringify(data),
            form_id: formID
          })          
        }).then(response => {
          resolve(response.text())
        })
      }).then(response => {
        let cookie = getCookie('wplg_'+formID)
        if (!cookie) {
          setCookie('wplg_'+formID,'true',7);
        }
        console.log(response)
        this.overlay.toggleAttribute('active')
        this.controls.toggleAttribute('active');
        this.playPauseMedia()
      })
    }
  
    checkForActions() {
      this.actions.forEach(element => {
        if ( 
          this.media.currentTime >= this.humanToComputer(element.time) && !this.actionsFinished.includes(element.time)
          ) {
            let cookie = getCookie('wplg_'+element.id)
            if (!cookie) {
              this.actionsFinished.push(element.time);
              this.rwd.classList.remove('active');
              this.fwd.classList.remove('active');
              this.media.currentTime = this.humanToComputer(element.time);
              this.stopVideo();
              this.overlay.toggleAttribute('active')
              this.overlay.innerHTML = null 
              let formContainer = document.createElement("div")
              formContainer.setAttribute('form_id', element.id)
              formContainer.classList.add('form-wrapper')
              let form = document.createElement('form')
              let submit = document.createElement('button')
              submit.setAttribute('type', 'submit')
              submit.innerHTML = element.submit_txt
              form.innerHTML = element.html
              form.append(submit)
              formContainer.append(form)
              form.addEventListener('submit', (event)=>{
                this.submitForm(event)
              })              
              this.overlay.append(formContainer)
              this.controls.toggleAttribute('active');
            }
          }
      });
    }
}
  
let wplgvideos = document.querySelectorAll('.wplgvideo')
wplgvideos.forEach(element => {
  new wplgVideo(element)
})