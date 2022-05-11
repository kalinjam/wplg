import JSZip from 'jszip';
import FileSaver from 'file-saver';

const download_csv_bttn = document.querySelector('#wplg_download_csv')
if (download_csv_bttn) {
	download_csv_bttn.addEventListener('click', ()=>{
		const promise = new Promise((resolve, reject) => {
			fetch( wplg_params.ajax_url, {
				method: 'post',
				body: new URLSearchParams({
					action: 'wplg_get_leads',	
				})          
			}).then(response=>{
				resolve(response.text())
			})
		})
		promise.then((response)=>{
			const zip = new JSZip()
			const items = JSON.parse(response)
			for (const [key, value] of Object.entries(items)) {
				const replacer = (key2, value2) => value2 === null ? '' : value2 // specify how you want to handle null values here
				// const header = Object.keys(value[0])

				let header = []
				value.forEach(element => {
					header.push(Object.keys(element))
				})
				header = header.flat()
				header = [...new Set(header)]
				
				const csv = [
				header.join(','), // header row first
				...value.map(row => header.map(fieldName => JSON.stringify(row[fieldName], replacer)).join(','))
				].join('\r\n')
				console.log(csv)				
				zip.file(key + '.csv', csv)
			}
			zip.generateAsync({ type: 'blob' }).then(function (content) {
				FileSaver.saveAs(content, 'wplg-leads.zip')
			})
		})
	})		
}

const settingsForm = document.querySelector('.wplg-settings-form')
if(settingsForm) {
	settingsForm.addEventListener('submit', ()=>{
		fetch( wplg_params.ajax_url, {
			method: 'post',
			body: new URLSearchParams({
			  action: 'wplg_video_settings_save',
			})          
		}).then(function (response) {
			console.log(JSON.parse(response))
		})
	})
}