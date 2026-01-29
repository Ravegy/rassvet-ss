document.addEventListener('DOMContentLoaded', () => {
	const b = document.querySelector('.btn-download');
	if (b) {
		b.addEventListener('click', (e) => {
			e.preventDefault();
			const el = document.getElementById('pdf-template');
			if (!el) return;
			const t = b.innerHTML;
			b.innerHTML = 'ГЕНЕРАЦИЯ...';
			b.style.opacity = '0.7';
			el.style.display = 'block';
			html2pdf().set({
				margin: 10,
				filename: 'Requisites_RASSVET-S.pdf',
				image: {
					type: 'jpeg',
					quality: 0.98
				},
				html2canvas: {
					scale: 2
				},
				jsPDF: {
					unit: 'mm',
					format: 'a4',
					orientation: 'portrait'
				}
			}).from(el).save().then(() => {
				el.style.display = 'none';
				b.innerHTML = t;
				b.style.opacity = '1'
			}).catch(() => {
				el.style.display = 'none';
				b.innerHTML = 'ОШИБКА';
			})
		})
	}
	const obs = new IntersectionObserver((e) => {
		e.forEach(i => {
			if (i.isIntersecting) i.target.classList.add('animate-visible')
		})
	}, {
		threshold: 0.1
	});
	document.querySelectorAll('.tech-card, .partner-card, .lf-item').forEach(e => {
		e.classList.add('animate-hidden');
		obs.observe(e)
	})
});