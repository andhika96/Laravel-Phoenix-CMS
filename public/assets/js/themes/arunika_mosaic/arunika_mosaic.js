// --- 1. SETUP COLOR PICKER ---
let colorMainList = ['#1FA675', '#9D00FF', '#1DA1F2', '#FF5733', '#FFC107', '#E91E63', '#6C5CE7'];
const pickerContainer = document.getElementById('color-picker-container');

const patterns = {
	// 0. NO PATTERN (Clean Mode)
	'none': {
		// SVG Kosong 1x1 pixel agar masking membuat layer jadi invisible
		top: `url("data:image/svg+xml,%3Csvg width='1' height='1' viewBox='0 0 1 1' xmlns='http://www.w3.org/2000/svg'%3E%3C/svg%3E")`,
		bottom: 'none',
		repeat: 'repeat', 
		size: 'auto', 
		composite: 'source-in', 
		display: 'none'
	},
	
	// 1. WINTER (Snowflakes - Full Screen)
	'winter': {
		top: `url("data:image/svg+xml,%3Csvg width='400' height='400' viewBox='0 0 400 400' xmlns='http://www.w3.org/2000/svg'%3E%3Cdefs%3E%3Csymbol id='f1' viewBox='0 0 24 24'%3E%3Cpath d='M12 2v20M2 12h20M4.9 4.9l14.2 14.2M4.9 19.1L19.1 4.9' fill='none' stroke='black' stroke-width='1.5' stroke-linecap='round'/%3E%3Cpath d='M12 6l-2-2M12 6l2-2M12 18l-2 2M12 18l2 2M6 12l-2-2M6 12l-2 2M18 12l2-2M18 12l2 2' fill='none' stroke='black' stroke-width='1.5' stroke-linecap='round'/%3E%3C/symbol%3E%3Csymbol id='f2' viewBox='0 0 24 24'%3E%3Cpath d='M12 0l3 9 9 3-9 3-3 9-3-9-9-3 9-3z' fill='black'/%3E%3C/symbol%3E%3C/defs%3E%3Cuse href='%23f1' x='20' y='20' width='40' height='40' /%3E%3Cuse href='%23f1' x='150' y='50' width='30' height='30' opacity='0.8'/%3E%3Cuse href='%23f1' x='300' y='10' width='50' height='50' /%3E%3Cuse href='%23f1' x='80' y='120' width='25' height='25' opacity='0.7'/%3E%3Cuse href='%23f1' x='350' y='150' width='35' height='35' /%3E%3Cuse href='%23f2' x='100' y='10' width='15' height='15' /%3E%3Cuse href='%23f2' x='250' y='80' width='20' height='20' opacity='0.6'/%3E%3Cuse href='%23f2' x='20' y='180' width='15' height='15' /%3E%3Cuse href='%23f2' x='200' y='200' width='20' height='20' opacity='0.5'/%3E%3Cuse href='%23f2' x='50' y='300' width='15' height='15' opacity='0.4'/%3E%3Ccircle cx='50' cy='60' r='2' fill='black'/%3E%3Ccircle cx='120' cy='180' r='3' fill='black'/%3E%3Ccircle cx='280' cy='50' r='2' fill='black'/%3E%3Ccircle cx='380' cy='280' r='3' fill='black' opacity='0.5'/%3E%3Ccircle cx='10' cy='100' r='2' fill='black'/%3E%3Ccircle cx='220' cy='120' r='1.5' fill='black'/%3E%3Ccircle cx='320' cy='220' r='2' fill='black' opacity='0.6'/%3E%3Ccircle cx='160' cy='30' r='1' fill='black'/%3E%3Ccircle cx='90' cy='250' r='2' fill='black' opacity='0.4'/%3E%3C/svg%3E")`,
		bottom: 'none',
		repeat: 'repeat',
		size: '400px 400px',
		composite: 'source-in',
		display: 'none'
	},

	// 2. CHRISTMAS
	'christmas': {
		top: `url("data:image/svg+xml,%3Csvg width='400' height='150' viewBox='0 0 400 150' xmlns='http://www.w3.org/2000/svg'%3E%3Cdefs%3E%3Csymbol id='tree' viewBox='0 0 24 24'%3E%3Cpath fill='none' stroke='black' stroke-width='1.5' stroke-linecap='round' d='M12 3l-4 6h3l-5 6h4l-6 6h16l-6-6h4l-5-6h3z'/%3E%3C/symbol%3E%3Csymbol id='gift' viewBox='0 0 24 24'%3E%3Crect x='3' y='8' width='18' height='14' rx='2' fill='none' stroke='black' stroke-width='1.5'/%3E%3Cpath d='M12 8v14M3 13h18M12 8c0-3-2.5-5-4-3s2 3 4 3zm0 0c0-3 2.5-5 4-3s-2 3-4 3z' fill='none' stroke='black' stroke-width='1.5'/%3E%3C/symbol%3E%3Csymbol id='candy' viewBox='0 0 24 24'%3E%3Cpath d='M9 20v-8a5 5 0 0 1 10 0v3' fill='none' stroke='black' stroke-width='2' stroke-linecap='round'/%3E%3Cpath d='M9 16h10M9 12h10' fill='none' stroke='black' stroke-width='1' stroke-dasharray='1 2'/%3E%3C/symbol%3E%3Csymbol id='sock' viewBox='0 0 24 24'%3E%3Cpath d='M9 3v10a4 4 0 0 0 4 4h4a3 3 0 0 0 0-6h-3V3H9z' fill='none' stroke='black' stroke-width='1.5'/%3E%3C/symbol%3E%3C/defs%3E%3Cuse href='%23tree' x='10' y='5' width='40' height='40' /%3E%3Cuse href='%23gift' x='90' y='10' width='30' height='30' /%3E%3Cuse href='%23candy' x='170' y='5' width='25' height='25' transform='rotate(15)' /%3E%3Cuse href='%23sock' x='250' y='10' width='30' height='30' /%3E%3Cuse href='%23tree' x='330' y='5' width='35' height='35' opacity='0.9'/%3E%3Cuse href='%23candy' x='50' y='75' width='30' height='30' opacity='0.7' transform='rotate(-20)'/%3E%3Cuse href='%23sock' x='130' y='70' width='35' height='35' opacity='0.8'/%3E%3Cuse href='%23tree' x='210' y='80' width='40' height='40' opacity='0.6'/%3E%3Cuse href='%23gift' x='290' y='70' width='25' height='25' opacity='0.7'/%3E%3Cuse href='%23candy' x='370' y='85' width='25' height='25' transform='rotate(10)' opacity='0.8'/%3E%3C/svg%3E")`,
		bottom: `url("data:image/svg+xml,%3Csvg width='400' height='150' viewBox='0 0 400 150' xmlns='http://www.w3.org/2000/svg'%3E%3Cdefs%3E%3Csymbol id='tree' viewBox='0 0 24 24'%3E%3Cpath fill='none' stroke='black' stroke-width='1.5' stroke-linecap='round' d='M12 3l-4 6h3l-5 6h4l-6 6h16l-6-6h4l-5-6h3z'/%3E%3C/symbol%3E%3Csymbol id='gift' viewBox='0 0 24 24'%3E%3Crect x='3' y='8' width='18' height='14' rx='2' fill='none' stroke='black' stroke-width='1.5'/%3E%3Cpath d='M12 8v14M3 13h18M12 8c0-3-2.5-5-4-3s2 3 4 3zm0 0c0-3 2.5-5 4-3s-2 3-4 3z' fill='none' stroke='black' stroke-width='1.5'/%3E%3C/symbol%3E%3Csymbol id='candy' viewBox='0 0 24 24'%3E%3Cpath d='M9 20v-8a5 5 0 0 1 10 0v3' fill='none' stroke='black' stroke-width='2' stroke-linecap='round'/%3E%3Cpath d='M9 16h10M9 12h10' fill='none' stroke='black' stroke-width='1' stroke-dasharray='1 2'/%3E%3C/symbol%3E%3Csymbol id='sock' viewBox='0 0 24 24'%3E%3Cpath d='M9 3v10a4 4 0 0 0 4 4h4a3 3 0 0 0 0-6h-3V3H9z' fill='none' stroke='black' stroke-width='1.5'/%3E%3C/symbol%3E%3C/defs%3E%3Cuse href='%23candy' x='50' y='60' width='30' height='30' opacity='0.7' transform='rotate(-20)'/%3E%3Cuse href='%23sock' x='130' y='55' width='35' height='35' opacity='0.8'/%3E%3Cuse href='%23tree' x='210' y='65' width='40' height='40' opacity='0.6'/%3E%3Cuse href='%23gift' x='290' y='55' width='25' height='25' opacity='0.7'/%3E%3Cuse href='%23candy' x='370' y='70' width='25' height='25' transform='rotate(10)' opacity='0.8'/%3E%3Cuse href='%23tree' x='10' y='115' width='40' height='40' /%3E%3Cuse href='%23gift' x='90' y='120' width='30' height='30' /%3E%3Cuse href='%23candy' x='170' y='115' width='25' height='25' transform='rotate(15)' /%3E%3Cuse href='%23sock' x='250' y='120' width='30' height='30' /%3E%3Cuse href='%23tree' x='330' y='115' width='35' height='35' opacity='0.9'/%3E%3C/svg%3E")`,
		repeat: 'repeat-x', size: 'auto 150px', composite: 'source-over', display: 'block'
	},

	// 3. IDUL FITRI (EID)
	'eid': {
		top: `url("data:image/svg+xml,%3Csvg width='400' height='150' viewBox='0 0 400 150' xmlns='http://www.w3.org/2000/svg'%3E%3Cdefs%3E%3Csymbol id='ketupat' viewBox='0 0 24 24'%3E%3Cpath d='M12 2l10 10-10 10L2 12z' fill='none' stroke='black' stroke-width='1.5'/%3E%3Cpath d='M7 7l10 10M17 7l-10 10M12 2v20M2 12h20' stroke='black' stroke-width='1' opacity='0.6'/%3E%3C/symbol%3E%3Csymbol id='moon' viewBox='0 0 24 24'%3E%3Cpath d='M12 3a9 9 0 1 0 9 9c0-.46-.04-.92-.1-1.36a5.389 5.389 0 0 1-4.4 2.26 5.403 5.403 0 0 1-5.4-5.4 5.389 5.389 0 0 1 2.26-4.4C12.92 3.04 12.46 3 12 3z' fill='black'/%3E%3C/symbol%3E%3Csymbol id='lantern' viewBox='0 0 24 24'%3E%3Cpath d='M12 2v3m0 16v1m-4-6l-2-6h12l-2 6H8zm4-12l3 3H9l3-3z' fill='none' stroke='black' stroke-width='1.5'/%3E%3Ccircle cx='12' cy='14' r='1' fill='black'/%3E%3C/symbol%3E%3Csymbol id='mosque' viewBox='0 0 24 24'%3E%3Cpath d='M3 22v-3c0-4 4-8 9-8s9 4 9 8v3H3z' fill='none' stroke='black' stroke-width='1.5'/%3E%3Cpath d='M12 2v4M12 11v11' stroke='black' stroke-width='1'/%3E%3Cpath d='M12 6c-2 0-4 1-4 3v2h8V9c0-2-2-3-4-3z' fill='none' stroke='black' stroke-width='1.5'/%3E%3C/symbol%3E%3C/defs%3E%3Cuse href='%23ketupat' x='10' y='5' width='40' height='40' /%3E%3Cuse href='%23lantern' x='90' y='20' width='30' height='30' /%3E%3Cuse href='%23moon' x='170' y='5' width='25' height='25' /%3E%3Cuse href='%23mosque' x='250' y='15' width='35' height='35' /%3E%3Cuse href='%23ketupat' x='330' y='10' width='35' height='35' opacity='0.8'/%3E%3Cuse href='%23moon' x='50' y='75' width='30' height='30' opacity='0.7' transform='rotate(-15)'/%3E%3Cuse href='%23mosque' x='130' y='85' width='40' height='40' opacity='0.6'/%3E%3Cuse href='%23ketupat' x='210' y='70' width='40' height='40' opacity='0.5'/%3E%3Cuse href='%23lantern' x='290' y='80' width='30' height='30' opacity='0.7'/%3E%3Cuse href='%23moon' x='370' y='65' width='25' height='25' opacity='0.8'/%3E%3C/svg%3E")`,
		bottom: `url("data:image/svg+xml,%3Csvg width='400' height='150' viewBox='0 0 400 150' xmlns='http://www.w3.org/2000/svg'%3E%3Cdefs%3E%3Csymbol id='ketupat' viewBox='0 0 24 24'%3E%3Cpath d='M12 2l10 10-10 10L2 12z' fill='none' stroke='black' stroke-width='1.5'/%3E%3Cpath d='M7 7l10 10M17 7l-10 10M12 2v20M2 12h20' stroke='black' stroke-width='1' opacity='0.6'/%3E%3C/symbol%3E%3Csymbol id='moon' viewBox='0 0 24 24'%3E%3Cpath d='M12 3a9 9 0 1 0 9 9c0-.46-.04-.92-.1-1.36a5.389 5.389 0 0 1-4.4 2.26 5.403 5.403 0 0 1-5.4-5.4 5.389 5.389 0 0 1 2.26-4.4C12.92 3.04 12.46 3 12 3z' fill='black'/%3E%3C/symbol%3E%3Csymbol id='lantern' viewBox='0 0 24 24'%3E%3Cpath d='M12 2v3m0 16v1m-4-6l-2-6h12l-2 6H8zm4-12l3 3H9l3-3z' fill='none' stroke='black' stroke-width='1.5'/%3E%3Ccircle cx='12' cy='14' r='1' fill='black'/%3E%3C/symbol%3E%3Csymbol id='mosque' viewBox='0 0 24 24'%3E%3Cpath d='M3 22v-3c0-4 4-8 9-8s9 4 9 8v3H3z' fill='none' stroke='black' stroke-width='1.5'/%3E%3Cpath d='M12 2v4M12 11v11' stroke='black' stroke-width='1'/%3E%3Cpath d='M12 6c-2 0-4 1-4 3v2h8V9c0-2-2-3-4-3z' fill='none' stroke='black' stroke-width='1.5'/%3E%3C/symbol%3E%3C/defs%3E%3Cuse href='%23moon' x='50' y='60' width='30' height='30' opacity='0.7' transform='rotate(-15)'/%3E%3Cuse href='%23mosque' x='130' y='70' width='40' height='40' opacity='0.6'/%3E%3Cuse href='%23ketupat' x='210' y='55' width='40' height='40' opacity='0.5'/%3E%3Cuse href='%23lantern' x='290' y='65' width='30' height='30' opacity='0.7'/%3E%3Cuse href='%23moon' x='370' y='50' width='25' height='25' opacity='0.8'/%3E%3Cuse href='%23ketupat' x='10' y='110' width='40' height='40' /%3E%3Cuse href='%23lantern' x='90' y='125' width='30' height='30' /%3E%3Cuse href='%23moon' x='170' y='110' width='25' height='25' /%3E%3Cuse href='%23mosque' x='250' y='120' width='35' height='35' /%3E%3Cuse href='%23ketupat' x='330' y='115' width='35' height='35' opacity='0.8'/%3E%3C/svg%3E")`,
		repeat: 'repeat-x', size: 'auto 150px', composite: 'source-over', display: 'block'
	},

	// 4. NEW YEAR (Fireworks, Trumpet, Hat, Cheers)
	'newyear': {
		top: `url("data:image/svg+xml,%3Csvg width='400' height='150' viewBox='0 0 400 150' xmlns='http://www.w3.org/2000/svg'%3E%3Cdefs%3E%3Csymbol id='firework' viewBox='0 0 24 24'%3E%3Cpath d='M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83' stroke='black' stroke-width='1.5' stroke-linecap='round'/%3E%3Ccircle cx='12' cy='12' r='2' fill='black'/%3E%3C/symbol%3E%3Csymbol id='trumpet' viewBox='0 0 24 24'%3E%3Cpath d='M5 8l10-2v12l-10-2v-8z' fill='none' stroke='black' stroke-width='1.5'/%3E%3Cpath d='M15 6l6-3v18l-6-3' stroke='black' stroke-width='1.5'/%3E%3C/symbol%3E%3Csymbol id='glass' viewBox='0 0 24 24'%3E%3Cpath d='M8 21h8M12 21v-5M6 4l6 11 6-11' fill='none' stroke='black' stroke-width='1.5'/%3E%3Cpath d='M6 4h12' stroke='black' stroke-width='1.5'/%3E%3C/symbol%3E%3Csymbol id='hat' viewBox='0 0 24 24'%3E%3Cpath d='M3 20h18L12 4z' fill='none' stroke='black' stroke-width='1.5'/%3E%3Cpath d='M12 2v2' stroke='black' stroke-width='1.5'/%3E%3C/symbol%3E%3C/defs%3E%3Cuse href='%23firework' x='10' y='5' width='40' height='40' /%3E%3Cuse href='%23trumpet' x='90' y='10' width='35' height='35' transform='rotate(15)'/%3E%3Cuse href='%23glass' x='170' y='5' width='25' height='25' /%3E%3Cuse href='%23hat' x='250' y='10' width='35' height='35' /%3E%3Cuse href='%23firework' x='330' y='5' width='35' height='35' opacity='0.8'/%3E%3Cuse href='%23hat' x='50' y='75' width='30' height='30' opacity='0.7' transform='rotate(-15)'/%3E%3Cuse href='%23glass' x='130' y='65' width='30' height='30' opacity='0.8'/%3E%3Cuse href='%23firework' x='210' y='70' width='40' height='40' opacity='0.5'/%3E%3Cuse href='%23trumpet' x='290' y='65' width='35' height='35' opacity='0.7'/%3E%3Cuse href='%23glass' x='370' y='80' width='25' height='25' opacity='0.8'/%3E%3C/svg%3E")`,
		bottom: `url("data:image/svg+xml,%3Csvg width='400' height='150' viewBox='0 0 400 150' xmlns='http://www.w3.org/2000/svg'%3E%3Cdefs%3E%3Csymbol id='firework' viewBox='0 0 24 24'%3E%3Cpath d='M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83' stroke='black' stroke-width='1.5' stroke-linecap='round'/%3E%3Ccircle cx='12' cy='12' r='2' fill='black'/%3E%3C/symbol%3E%3Csymbol id='trumpet' viewBox='0 0 24 24'%3E%3Cpath d='M5 8l10-2v12l-10-2v-8z' fill='none' stroke='black' stroke-width='1.5'/%3E%3Cpath d='M15 6l6-3v18l-6-3' stroke='black' stroke-width='1.5'/%3E%3C/symbol%3E%3Csymbol id='glass' viewBox='0 0 24 24'%3E%3Cpath d='M8 21h8M12 21v-5M6 4l6 11 6-11' fill='none' stroke='black' stroke-width='1.5'/%3E%3Cpath d='M6 4h12' stroke='black' stroke-width='1.5'/%3E%3C/symbol%3E%3Csymbol id='hat' viewBox='0 0 24 24'%3E%3Cpath d='M3 20h18L12 4z' fill='none' stroke='black' stroke-width='1.5'/%3E%3Cpath d='M12 2v2' stroke='black' stroke-width='1.5'/%3E%3C/symbol%3E%3C/defs%3E%3Cuse href='%23hat' x='50' y='60' width='30' height='30' opacity='0.7' transform='rotate(-15)'/%3E%3Cuse href='%23glass' x='130' y='50' width='30' height='30' opacity='0.8'/%3E%3Cuse href='%23firework' x='210' y='55' width='40' height='40' opacity='0.5'/%3E%3Cuse href='%23trumpet' x='290' y='50' width='35' height='35' opacity='0.7'/%3E%3Cuse href='%23glass' x='370' y='65' width='25' height='25' opacity='0.8'/%3E%3Cuse href='%23firework' x='10' y='110' width='40' height='40' /%3E%3Cuse href='%23trumpet' x='90' y='115' width='35' height='35' transform='rotate(15)'/%3E%3Cuse href='%23glass' x='170' y='110' width='25' height='25' /%3E%3Cuse href='%23hat' x='250' y='115' width='35' height='35' /%3E%3Cuse href='%23firework' x='330' y='110' width='35' height='35' opacity='0.8'/%3E%3C/svg%3E")`,
		repeat: 'repeat-x', size: 'auto 150px', composite: 'source-over', display: 'block'
	},

	// 5. VALENTINE (Heart, Cupid, Envelope, Rose)
	'valentine': {
		top: `url("data:image/svg+xml,%3Csvg width='400' height='150' viewBox='0 0 400 150' xmlns='http://www.w3.org/2000/svg'%3E%3Cdefs%3E%3Csymbol id='heart' viewBox='0 0 24 24'%3E%3Cpath d='M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z' fill='none' stroke='black' stroke-width='1.5'/%3E%3C/symbol%3E%3Csymbol id='arrow' viewBox='0 0 24 24'%3E%3Cpath d='M2 12h20M20 10l2 2-2 2M2 10l-2 2 2 2' stroke='black' stroke-width='1.5'/%3E%3Cpath d='M12 2l2 2-2 2M12 22l-2-2 2-2' stroke='black' stroke-width='1.5' opacity='0'/%3E%3C/symbol%3E%3Csymbol id='letter' viewBox='0 0 24 24'%3E%3Cpath d='M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z' fill='none' stroke='black' stroke-width='1.5'/%3E%3C/symbol%3E%3Csymbol id='rose' viewBox='0 0 24 24'%3E%3Ccircle cx='12' cy='10' r='3' stroke='black' stroke-width='1.5'/%3E%3Cpath d='M12 13v8M12 16l-3-2M12 16l3-2' stroke='black' stroke-width='1.5'/%3E%3C/symbol%3E%3C/defs%3E%3Cuse href='%23heart' x='10' y='10' width='35' height='35' /%3E%3Cuse href='%23arrow' x='90' y='20' width='40' height='40' transform='rotate(-45 110 40)'/%3E%3Cuse href='%23letter' x='170' y='10' width='35' height='35' /%3E%3Cuse href='%23rose' x='250' y='5' width='30' height='30' /%3E%3Cuse href='%23heart' x='330' y='15' width='30' height='30' opacity='0.8'/%3E%3Cuse href='%23letter' x='50' y='75' width='30' height='30' opacity='0.7'/%3E%3Cuse href='%23heart' x='130' y='65' width='35' height='35' opacity='0.8'/%3E%3Cuse href='%23rose' x='210' y='75' width='35' height='35' opacity='0.6'/%3E%3Cuse href='%23arrow' x='290' y='70' width='35' height='35' opacity='0.7' transform='rotate(30 307 87)'/%3E%3Cuse href='%23heart' x='370' y='80' width='25' height='25' opacity='0.8'/%3E%3C/svg%3E")`,
		bottom: `url("data:image/svg+xml,%3Csvg width='400' height='150' viewBox='0 0 400 150' xmlns='http://www.w3.org/2000/svg'%3E%3Cdefs%3E%3Csymbol id='heart' viewBox='0 0 24 24'%3E%3Cpath d='M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z' fill='none' stroke='black' stroke-width='1.5'/%3E%3C/symbol%3E%3Csymbol id='arrow' viewBox='0 0 24 24'%3E%3Cpath d='M2 12h20M20 10l2 2-2 2M2 10l-2 2 2 2' stroke='black' stroke-width='1.5'/%3E%3Cpath d='M12 2l2 2-2 2M12 22l-2-2 2-2' stroke='black' stroke-width='1.5' opacity='0'/%3E%3C/symbol%3E%3Csymbol id='letter' viewBox='0 0 24 24'%3E%3Cpath d='M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z' fill='none' stroke='black' stroke-width='1.5'/%3E%3C/symbol%3E%3Csymbol id='rose' viewBox='0 0 24 24'%3E%3Ccircle cx='12' cy='10' r='3' stroke='black' stroke-width='1.5'/%3E%3Cpath d='M12 13v8M12 16l-3-2M12 16l3-2' stroke='black' stroke-width='1.5'/%3E%3C/symbol%3E%3C/defs%3E%3Cuse href='%23letter' x='50' y='60' width='30' height='30' opacity='0.7'/%3E%3Cuse href='%23heart' x='130' y='50' width='35' height='35' opacity='0.8'/%3E%3Cuse href='%23rose' x='210' y='60' width='35' height='35' opacity='0.6'/%3E%3Cuse href='%23arrow' x='290' y='55' width='35' height='35' opacity='0.7' transform='rotate(30 307 72)'/%3E%3Cuse href='%23heart' x='370' y='65' width='25' height='25' opacity='0.8'/%3E%3Cuse href='%23heart' x='10' y='115' width='35' height='35' /%3E%3Cuse href='%23arrow' x='90' y='125' width='40' height='40' transform='rotate(-45 110 145)'/%3E%3Cuse href='%23letter' x='170' y='115' width='35' height='35' /%3E%3Cuse href='%23rose' x='250' y='110' width='30' height='30' /%3E%3Cuse href='%23heart' x='330' y='120' width='30' height='30' opacity='0.8'/%3E%3C/svg%3E")`,
		repeat: 'repeat-x', size: 'auto 150px', composite: 'source-over', display: 'block'
	},

	// 6. INDEPENDENCE (Garuda, Flag, Bamboo, Monas)
	'independence': {
		top: `url("data:image/svg+xml,%3Csvg width='400' height='150' viewBox='0 0 400 150' xmlns='http://www.w3.org/2000/svg'%3E%3Cdefs%3E%3Csymbol id='monas' viewBox='0 0 24 24'%3E%3Cpath d='M12 2l-1 2h2l-1-2zm-2 2l-1 14h6l-1-14h-4zm-4 14h12v4H6v-4z' fill='none' stroke='black' stroke-width='1.5'/%3E%3C/symbol%3E%3Csymbol id='garuda' viewBox='0 0 24 24'%3E%3Cpath d='M2 8l10 4 10-4-10-6-10 6zm0 0l4 8h12l4-8' fill='none' stroke='black' stroke-width='1.5'/%3E%3Cpath d='M12 12v10' stroke='black' stroke-width='1.5'/%3E%3C/symbol%3E%3Csymbol id='flag' viewBox='0 0 24 24'%3E%3Crect x='4' y='4' width='16' height='10' fill='none' stroke='black' stroke-width='1.5'/%3E%3Cpath d='M4 9h16' stroke='black' stroke-width='1.5'/%3E%3Cpath d='M4 4v18' stroke='black' stroke-width='1.5'/%3E%3C/symbol%3E%3Csymbol id='bamboo' viewBox='0 0 24 24'%3E%3Cpath d='M12 2l-4 20h8L12 2z' fill='none' stroke='black' stroke-width='1.5'/%3E%3Cpath d='M10 10h4M10 16h4' stroke='black' stroke-width='1.5'/%3E%3C/symbol%3E%3C/defs%3E%3Cuse href='%23garuda' x='10' y='5' width='40' height='40' /%3E%3Cuse href='%23flag' x='90' y='10' width='35' height='35' /%3E%3Cuse href='%23bamboo' x='170' y='5' width='25' height='25' transform='rotate(15)'/%3E%3Cuse href='%23monas' x='250' y='10' width='30' height='30' /%3E%3Cuse href='%23garuda' x='330' y='5' width='35' height='35' opacity='0.9'/%3E%3Cuse href='%23bamboo' x='50' y='75' width='30' height='30' opacity='0.7' transform='rotate(-10)'/%3E%3Cuse href='%23monas' x='130' y='70' width='35' height='35' opacity='0.8'/%3E%3Cuse href='%23garuda' x='210' y='80' width='40' height='40' opacity='0.6'/%3E%3Cuse href='%23flag' x='290' y='70' width='30' height='30' opacity='0.7'/%3E%3Cuse href='%23bamboo' x='370' y='85' width='25' height='25' opacity='0.8'/%3E%3C/svg%3E")`,
		bottom: `url("data:image/svg+xml,%3Csvg width='400' height='150' viewBox='0 0 400 150' xmlns='http://www.w3.org/2000/svg'%3E%3Cdefs%3E%3Csymbol id='monas' viewBox='0 0 24 24'%3E%3Cpath d='M12 2l-1 2h2l-1-2zm-2 2l-1 14h6l-1-14h-4zm-4 14h12v4H6v-4z' fill='none' stroke='black' stroke-width='1.5'/%3E%3C/symbol%3E%3Csymbol id='garuda' viewBox='0 0 24 24'%3E%3Cpath d='M2 8l10 4 10-4-10-6-10 6zm0 0l4 8h12l4-8' fill='none' stroke='black' stroke-width='1.5'/%3E%3Cpath d='M12 12v10' stroke='black' stroke-width='1.5'/%3E%3C/symbol%3E%3Csymbol id='flag' viewBox='0 0 24 24'%3E%3Crect x='4' y='4' width='16' height='10' fill='none' stroke='black' stroke-width='1.5'/%3E%3Cpath d='M4 9h16' stroke='black' stroke-width='1.5'/%3E%3Cpath d='M4 4v18' stroke='black' stroke-width='1.5'/%3E%3C/symbol%3E%3Csymbol id='bamboo' viewBox='0 0 24 24'%3E%3Cpath d='M12 2l-4 20h8L12 2z' fill='none' stroke='black' stroke-width='1.5'/%3E%3Cpath d='M10 10h4M10 16h4' stroke='black' stroke-width='1.5'/%3E%3C/symbol%3E%3C/defs%3E%3Cuse href='%23bamboo' x='50' y='60' width='30' height='30' opacity='0.7' transform='rotate(-10)'/%3E%3Cuse href='%23monas' x='130' y='55' width='35' height='35' opacity='0.8'/%3E%3Cuse href='%23garuda' x='210' y='65' width='40' height='40' opacity='0.6'/%3E%3Cuse href='%23flag' x='290' y='55' width='30' height='30' opacity='0.7'/%3E%3Cuse href='%23bamboo' x='370' y='70' width='25' height='25' opacity='0.8'/%3E%3Cuse href='%23garuda' x='10' y='110' width='40' height='40' /%3E%3Cuse href='%23flag' x='90' y='115' width='35' height='35' /%3E%3Cuse href='%23bamboo' x='170' y='110' width='25' height='25' transform='rotate(15)'/%3E%3Cuse href='%23monas' x='250' y='115' width='30' height='30' /%3E%3Cuse href='%23garuda' x='330' y='110' width='35' height='35' opacity='0.9'/%3E%3C/svg%3E")`,
		repeat: 'repeat-x', size: 'auto 150px', composite: 'source-over', display: 'block'
	},

	// 7. IMLEK (Lantern, Coin, Fan, Cracker)
	'imlek': {
		top: `url("data:image/svg+xml,%3Csvg width='400' height='150' viewBox='0 0 400 150' xmlns='http://www.w3.org/2000/svg'%3E%3Cdefs%3E%3Csymbol id='lampion' viewBox='0 0 24 24'%3E%3Crect x='6' y='4' width='12' height='16' rx='4' fill='none' stroke='black' stroke-width='1.5'/%3E%3Cpath d='M12 2v2M12 20v2M6 8h12M6 16h12' stroke='black' stroke-width='1.5'/%3E%3C/symbol%3E%3Csymbol id='coin' viewBox='0 0 24 24'%3E%3Ccircle cx='12' cy='12' r='9' fill='none' stroke='black' stroke-width='1.5'/%3E%3Crect x='9' y='9' width='6' height='6' stroke='black' stroke-width='1.5'/%3E%3C/symbol%3E%3Csymbol id='fan' viewBox='0 0 24 24'%3E%3Cpath d='M2 12c0-5.5 4.5-10 10-10s10 4.5 10 10h-20z' fill='none' stroke='black' stroke-width='1.5'/%3E%3Cpath d='M12 12v3' stroke='black' stroke-width='1.5'/%3E%3C/symbol%3E%3Csymbol id='cracker' viewBox='0 0 24 24'%3E%3Cpath d='M8 2h8v16H8z' fill='none' stroke='black' stroke-width='1.5'/%3E%3Cpath d='M12 18v4' stroke='black' stroke-width='1.5'/%3E%3Cpath d='M6 6l2-2M18 6l-2-2' stroke='black' stroke-width='1.5'/%3E%3C/symbol%3E%3C/defs%3E%3Cuse href='%23lampion' x='10' y='5' width='35' height='35' /%3E%3Cuse href='%23coin' x='90' y='10' width='30' height='30' /%3E%3Cuse href='%23fan' x='170' y='5' width='35' height='35' /%3E%3Cuse href='%23cracker' x='250' y='10' width='30' height='30' /%3E%3Cuse href='%23lampion' x='330' y='5' width='35' height='35' opacity='0.9'/%3E%3Cuse href='%23fan' x='50' y='75' width='35' height='35' opacity='0.7'/%3E%3Cuse href='%23cracker' x='130' y='70' width='30' height='30' opacity='0.8'/%3E%3Cuse href='%23lampion' x='210' y='80' width='35' height='35' opacity='0.6'/%3E%3Cuse href='%23coin' x='290' y='70' width='25' height='25' opacity='0.7'/%3E%3Cuse href='%23fan' x='370' y='85' width='30' height='30' opacity='0.8'/%3E%3C/svg%3E")`,
		bottom: `url("data:image/svg+xml,%3Csvg width='400' height='150' viewBox='0 0 400 150' xmlns='http://www.w3.org/2000/svg'%3E%3Cdefs%3E%3Csymbol id='lampion' viewBox='0 0 24 24'%3E%3Crect x='6' y='4' width='12' height='16' rx='4' fill='none' stroke='black' stroke-width='1.5'/%3E%3Cpath d='M12 2v2M12 20v2M6 8h12M6 16h12' stroke='black' stroke-width='1.5'/%3E%3C/symbol%3E%3Csymbol id='coin' viewBox='0 0 24 24'%3E%3Ccircle cx='12' cy='12' r='9' fill='none' stroke='black' stroke-width='1.5'/%3E%3Crect x='9' y='9' width='6' height='6' stroke='black' stroke-width='1.5'/%3E%3C/symbol%3E%3Csymbol id='fan' viewBox='0 0 24 24'%3E%3Cpath d='M2 12c0-5.5 4.5-10 10-10s10 4.5 10 10h-20z' fill='none' stroke='black' stroke-width='1.5'/%3E%3Cpath d='M12 12v3' stroke='black' stroke-width='1.5'/%3E%3C/symbol%3E%3Csymbol id='cracker' viewBox='0 0 24 24'%3E%3Cpath d='M8 2h8v16H8z' fill='none' stroke='black' stroke-width='1.5'/%3E%3Cpath d='M12 18v4' stroke='black' stroke-width='1.5'/%3E%3Cpath d='M6 6l2-2M18 6l-2-2' stroke='black' stroke-width='1.5'/%3E%3C/symbol%3E%3C/defs%3E%3Cuse href='%23fan' x='50' y='60' width='35' height='35' opacity='0.7'/%3E%3Cuse href='%23cracker' x='130' y='55' width='30' height='30' opacity='0.8'/%3E%3Cuse href='%23lampion' x='210' y='65' width='35' height='35' opacity='0.6'/%3E%3Cuse href='%23coin' x='290' y='55' width='25' height='25' opacity='0.7'/%3E%3Cuse href='%23fan' x='370' y='70' width='30' height='30' opacity='0.8'/%3E%3Cuse href='%23lampion' x='10' y='110' width='35' height='35' /%3E%3Cuse href='%23coin' x='90' y='115' width='30' height='30' /%3E%3Cuse href='%23fan' x='170' y='110' width='35' height='35' /%3E%3Cuse href='%23cracker' x='250' y='115' width='30' height='30' /%3E%3Cuse href='%23lampion' x='330' y='110' width='35' height='35' opacity='0.9'/%3E%3C/svg%3E")`,
		repeat: 'repeat-x', size: 'auto 150px', composite: 'source-over', display: 'block'
	}
};

colorMainList.forEach(color => 
{
	const col = document.createElement('div');
	col.className = 'col-3'; 
	col.innerHTML = `<div class="ph-color-switch" style="background-color: ${color};" onclick="changeMainColor('${color}')" title="${color}"></div>`;
	pickerContainer.appendChild(col);
});

function changeMainColor(color) 
{
	document.documentElement.style.setProperty('--ph-theme-primary', color);
	localStorage.setItem('theme-color', color);
}

// --- 2. SIDEBAR & SCROLLBAR LOGIC ---
const sidebar = document.getElementById('sidebar');
const scrollContent = document.getElementById('sidebar-scroll-content');
let sbInstance = null;

function initSimpleBar() 
{
	if ( ! sbInstance) 
	{
		sbInstance = new SimpleBar(scrollContent, 
		{ 
			autoHide: true,
			scrollbarMinSize: 30, 
			clickOnTrack: false
		});
	}
}

function toggleSidebar() 
{
	// UPDATE: Class updated to 'ph-expanded'
	sidebar.classList.toggle('ph-expanded');
	const isExpanded = sidebar.classList.contains('ph-expanded');
	localStorage.setItem('sidebar-state', isExpanded ? 'expanded' : 'collapsed');

	if (sbInstance) 
	{
		setTimeout(() =>
		{
			sbInstance.recalculate();

		}, 350); 
	}
}

// --- 3. TOOLTIP & POPOVER LOGIC (INSTANT SWITCH FIX) ---
// UPDATE: Selector tetap menggunakan class .ph-sidebar .list-group-item
const navLinkItems = document.querySelectorAll('.ph-sidebar .list-group-item');

// Helper 1: Cari elemen popover (sibling)
function getPopover(currentItem) 
{
	let nextEl = currentItem.nextElementSibling;
	while (nextEl) 
	{
		// UPDATE: Selector updated to 'ph-floating-submenu'
		if (nextEl.classList.contains('ph-floating-submenu')) return nextEl;
		if (nextEl.tagName === 'a') return null; 
		nextEl = nextEl.nextElementSibling;
	}
	return null;
}

// Helper 2: Tutup paksa semua popover LAIN yang sedang terbuka
function closeAllOtherPopovers(exceptPopover) 
{
	// UPDATE: Selector updated
	const allPopovers = document.querySelectorAll('.ph-floating-submenu.ph-show-popover');
	
	allPopovers.forEach(p => 
	{
		if (p !== exceptPopover) 
		{
			p.classList.remove('ph-show-popover'); // UPDATE: Selector updated
			
			if (p.dataset.timeoutId) 
			{
				clearTimeout(parseInt(p.dataset.timeoutId)); 
				p.dataset.timeoutId = '';
			}
		}
	});
	
	// Opsional: Tutup juga semua tooltip lain agar bersih
	const allTooltips = document.querySelectorAll('.ph-custom-tooltip.ph-show-tooltip');
	allTooltips.forEach(t => t.classList.remove('ph-show-tooltip'));
}

navLinkItems.forEach(item => 
{
	// --- 1. MOUSE ENTER (MASUK) ---
	item.addEventListener('mouseenter', function() 
	{
		if (sidebar.classList.contains('ph-expanded')) return; // UPDATE: ph-expanded
		
		const rect = this.getBoundingClientRect(); 
		const tooltip = this.querySelector('.ph-custom-tooltip'); // UPDATE
		const popover = getPopover(this);

		// A. MATIKAN POPOVER LAIN SEBELUM MEMBUKA YANG BARU
		closeAllOtherPopovers(popover);

		// B. LOGIC TOOLTIP
		if (tooltip) 
		{
			const topPos = rect.top + (rect.height / 2) - (tooltip.offsetHeight / 2);
			tooltip.style.top = topPos + 'px';
			tooltip.style.left = (rect.right + 10) + 'px'; 
			tooltip.classList.add('ph-show-tooltip'); // UPDATE
		}

		// C. LOGIC POPOVER
		if (popover) 
		{
			// Reset timer popover sendiri
			if (popover.dataset.timeoutId) 
			{
				clearTimeout(parseInt(popover.dataset.timeoutId));
				popover.dataset.timeoutId = '';
			}

			// Tampilkan
			popover.style.top = rect.top + 'px'; 
			popover.style.left = (rect.right + 10) + 'px';
			popover.classList.add('ph-show-popover'); // UPDATE

			// Setup listener pada popover (Hanya sekali per elemen)
			if ( ! popover.dataset.hasListener) 
			{
				popover.addEventListener('mouseenter', function() 
				{
					if (this.dataset.timeoutId) {
						clearTimeout(parseInt(this.dataset.timeoutId));
						this.dataset.timeoutId = '';
					}
				});

				popover.addEventListener('mouseleave', function() 
				{
					this.classList.remove('ph-show-popover'); // UPDATE
				});

				popover.dataset.hasListener = 'true';
			}
		}
	});

	// --- 2. MOUSE LEAVE (KELUAR) ---
	item.addEventListener('mouseleave', function() 
	{
		 const tooltip = this.querySelector('.ph-custom-tooltip');
		 const popover = getPopover(this);

		 // Tooltip langsung hilang
		 if (tooltip) tooltip.classList.remove('ph-show-tooltip'); // UPDATE

		 // Popover hilang pakai DELAY
		 if (popover) 
		 {
			const timeoutId = setTimeout(() => 
			{
				popover.classList.remove('ph-show-popover'); // UPDATE
			}, 300);
			
			popover.dataset.timeoutId = timeoutId;
		 }
	});
});

// --- 4. THEME & INIT ---
function toggleTheme() 
{
	const htmlTag = document.documentElement;
	const currentTheme = htmlTag.getAttribute('data-bs-theme'); // Bootstrap 5.3 uses data-bs-theme
	const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
	
	htmlTag.setAttribute('data-bs-theme', newTheme);
	
	const icon = document.getElementById('theme-icon');
	icon.className = newTheme === 'light' ? 'fas fa-sun' : 'fas fa-moon';
	
	localStorage.setItem('theme', newTheme);
}

// --- FUNCTION CHANGE PATTERN ---
function changePattern(type) 
{
	if (patterns[type]) 
	{
		const p = patterns[type];
		
		// Set Variable Utama
		document.documentElement.style.setProperty('--ph-pattern-top', p.top);
		document.documentElement.style.setProperty('--ph-pattern-bottom', p.bottom);
		
		// Set Variable Setting
		document.documentElement.style.setProperty('--ph-mask-repeat', p.repeat);
		document.documentElement.style.setProperty('--ph-mask-size', p.size);
		document.documentElement.style.setProperty('--ph-mask-composite', p.composite);
		
		// FIX: Set Display After (Untuk sembunyikan bawah saat Winter)
		document.documentElement.style.setProperty('--ph-after-display', p.display);
		
		localStorage.setItem('theme-pattern', type);
	}
}

document.addEventListener('DOMContentLoaded', () => 
{
	// 1. Load Theme
	const savedTheme = localStorage.getItem('theme') || 'dark';
	const icon = document.getElementById('theme-icon');
	if (icon) 
	{
		icon.className = savedTheme === 'light' ? 'fas fa-sun' : 'fas fa-moon';
	}

	// Load Pattern tersimpan (default winter jika tidak ada)
	const savedPattern = localStorage.getItem('theme-pattern') || 'winter';
	changePattern(savedPattern);

	// 2. Sidebar State
	// Perbaikan: Class sidebar ada di HTML statis, pengecekan dilakukan di inline script atas untuk mencegah FOUC
	// tapi kita pastikan SimpleBar di-init
	initSimpleBar();

	// 3. Smooth Animation Fix
	requestAnimationFrame(() => 
	{
		setTimeout(() => 
		{
			sidebar.classList.remove('ph-no-transition'); // UPDATE
		}, 100);
	});
});