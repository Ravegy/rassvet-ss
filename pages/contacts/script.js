document.addEventListener('DOMContentLoaded', () => {
	const observer = new IntersectionObserver((entries) => {
		entries.forEach(entry => {
			if (entry.isIntersecting) {
				entry.target.classList.add('animate-visible')
			}
		})
	}, {
		threshold: 0.1
	});
	document.querySelectorAll('.tech-card').forEach(card => {
		card.classList.add('animate-hidden');
		observer.observe(card)
	});
	document.querySelectorAll('.copy-text, .copy-btn').forEach(btn => {
		btn.addEventListener('click', () => {
			const textToCopy = btn.getAttribute('data-copy');
			if (textToCopy) {
				navigator.clipboard.writeText(textToCopy).then(() => {
					const originalHTML = btn.innerHTML;
					btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>';
					btn.style.color = '#ff3333';
					setTimeout(() => {
						btn.innerHTML = originalHTML;
						btn.style.color = ''
					}, 1500)
				})
			}
		})
	});
	const form = document.querySelector('.contact-form');
	if (form) {
		form.addEventListener('submit', (e) => {
			const btn = form.querySelector('button[type="submit"]');
			if (btn) {
				const originalText = btn.innerHTML;
				btn.innerHTML = 'ОТПРАВЛЕНО';
				btn.style.background = '#fff';
				btn.style.color = '#000';
				setTimeout(() => {
					btn.innerHTML = originalText;
					btn.style.background = '';
					btn.style.color = '';
					form.reset();
					const fn = document.getElementById('fileName');
					if (fn) {
						fn.textContent = 'Прикрепить файл (фото, PDF, DOCX)';
						fn.style.color = '#888'
					}
				}, 3000)
			}
		})
	}
	const phoneInput = document.querySelector('input[type="tel"]');
	if (phoneInput) {
		phoneInput.addEventListener('input', (e) => {
			let x = e.target.value.replace(/\D/g, '').match(/(\d{0,1})(\d{0,3})(\d{0,3})(\d{0,2})(\d{0,2})/);
			if (!x[2] && x[1] !== '') {
				e.target.value = x[1] === '7' ? '+7 ' : '+7 ' + x[1]
			} else {
				e.target.value = !x[2] ? x[1] : '+7 (' + x[2] + (x[3] ? ') ' + x[3] : '') + (x[4] ? '-' + x[4] : '') + (x[5] ? '-' + x[5] : '')
			}
		})
	}
	const fileInput = document.getElementById('formFile');
	const fileNameDisplay = document.getElementById('fileName');
	if (fileInput && fileNameDisplay) {
		fileInput.addEventListener('change', function() {
			if (this.files && this.files.length > 0) {
				fileNameDisplay.textContent = this.files[0].name;
				fileNameDisplay.style.color = '#ffffff';
				fileNameDisplay.style.fontWeight = '600'
			} else {
				fileNameDisplay.textContent = 'Прикрепить файл (фото, PDF, DOCX)';
				fileNameDisplay.style.color = '#888888';
				fileNameDisplay.style.fontWeight = '500'
			}
		})
	}
	const marker = document.querySelector('.map-marker');
	if (marker) {
		setInterval(() => {
			marker.classList.toggle('pulse')
		}, 2000)
	}
});