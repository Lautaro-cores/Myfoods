<?php
session_start();

// Si el usuario no está logueado, redirigir (seguir la convención de otras páginas)
if (!isset($_SESSION['userId'])) {
	header("Location: logIn.php");
}

// Banco de 20 preguntas sobre comida y cocina. Cada pregunta tiene: texto, 4 opciones y el índice de la respuesta correcta (0-3)
$questions = [
	['q' => '¿Cuál es el ingrediente principal del guacamole?', 'choices' => ['Tomate', 'Aguacate', 'Cebolla', 'Pimiento'], 'answer' => 1, 'explanation' => 'El aguacate aporta la textura cremosa y es la base tradicional del guacamole.'],
	['q' => '¿Qué tipo de pasta tiene forma de tubos cortos y anchos?', 'choices' => ['Spaghetti', 'Penne', 'Fusilli', 'Linguine'], 'answer' => 1, 'explanation' => 'El penne son tubos cortos y diagonales, muy usados con salsas que se adhieren en su interior.'],
	['q' => '¿Qué condimento tradicional se usa para hacer ceviche?', 'choices' => ['Vinagre de vino', 'Jugo de limón', 'Salsa de soja', 'Crema'], 'answer' => 1, 'explanation' => 'El jugo de limón (o lima) “cocina” el pescado al desnaturalizar las proteínas y es esencial en el ceviche.'],
	['q' => '¿Qué tipo de queso es típico en la pizza napolitana?', 'choices' => ['Parmesano', 'Gouda', 'Mozzarella', 'Cheddar'], 'answer' => 2, 'explanation' => 'La mozzarella (fresca) es la más usada en la pizza napolitana por su textura y fundido.'],
	['q' => '¿Cuál de estos métodos NO es una forma de conservar alimentos?', 'choices' => ['Fermentación', 'Encapsulado', 'Salazón', 'Deshidratación'], 'answer' => 1, 'explanation' => 'El encapsulado no es un método tradicional de conservación; fermentación, salazón y deshidratación sí lo son.'],
	['q' => '¿Qué corte de carne es conocido como ideal para estofados por su contenido de colágeno?', 'choices' => ['Lomo', 'Pecho', 'Solomillo', 'Filete'], 'answer' => 1, 'explanation' => 'El pecho (brisket) tiene tejido conectivo que, con cocción larga, se transforma en colágeno gelificado, dando sabor y textura.'],
	['q' => '¿Cuál es el ingrediente principal del hummus?', 'choices' => ['Lentejas', 'Garbanzos', 'Frijoles', 'Maíz'], 'answer' => 1, 'explanation' => 'El hummus se hace con garbanzos cocidos y triturados, aliñados con tahini y limón.'],
	['q' => '¿Qué tipo de azúcar se usa habitualmente para caramelizar en recetas?', 'choices' => ['Glucosa', 'Azúcar moreno', 'Azúcar granulada', 'Azúcar invertido'], 'answer' => 2, 'explanation' => 'El azúcar granulada (blanca) se carameliza fácilmente al calentarse y es la más común para caramelo.'],
	['q' => '¿Cómo se llama la sopa fría tradicional de España hecha con tomate?', 'choices' => ['Gazpacho', 'Minestrone', 'Ratatouille', 'Borscht'], 'answer' => 0, 'explanation' => 'El gazpacho es una sopa fría a base de tomate, pimiento y pepino, típica del sur de España.'],
	['q' => '¿Qué levadura se usa para hacer pan tradicionalmente?', 'choices' => ['Levadura química', 'Bicarbonato', 'Levadura fresca ', 'Polvo de hornear'], 'answer' => 2, 'explanation' => 'La levadura fresca o seca de pan (Saccharomyces cerevisiae) se usa para fermentar y leudar panes.'],
	['q' => '¿Cuál es el nombre del corte de pescado ideal para sashimi?', 'choices' => ['Atún', 'Bacalao', 'Salmón ahumado', 'Anchoa'], 'answer' => 0, 'explanation' => 'El atún (y también el salmón fresco) es popular para sashimi por su textura y sabor; aquí la opción correcta es atún.'],
	['q' => '¿Qué elemento se añade a una mayonesa para emulsionarla?', 'choices' => ['Agua caliente', 'Aceite solo', 'Yema de huevo', 'Harina'], 'answer' => 2, 'explanation' => 'La yema de huevo contiene lecitina, un emulsionante natural que ayuda a formar la mayonesa.'],
	['q' => '¿Cuál es la técnica culinaria tradicional argentina que consiste en cocinar carne (generalmente asada) atravesada por una cruz de hierro y expuesta al fuego lento y vertical?', 'choices' => ['Parrilla', 'A la plancha', 'Al rescoldo', 'Al asador'], 'answer' => 3, 'explanation' => 'El "Asador" o "Asado a la estaca" es una técnica gauchesca en la que la carne se cocina muy lentamente en posición vertical junto a las brasas, resultando en un exterior crujiente y un interior muy jugoso.'],
	['q' => '¿Qué proceso microbiano transforma el azúcar de la leche en ácido láctico, esencial para la producción de yogurt y algunos quesos?', 'choices' => ['Coagulación', 'Pasteurización', 'Fermentación láctica', 'Hidrólisis'], 'answer' => 2, 'explanation' => 'La fermentación láctica es llevada a cabo por bacterias beneficiosas que acidifican la leche, lo que provoca que se espese y desarrolle el sabor característico de productos como el yogurt o el kéfir.'],
   	['q' => '¿Qué tipo de salsa o aderezo picante a base de perejil, ajo, ají molido y aceite es imprescindible para acompañar el asado argentino?', 'choices' => ['Salsa criolla', 'Pesto', 'Chimichurri', 'Salsa de tomate'], 'answer' => 2, 'explanation' => 'El Chimichurri es una salsa o aderezo icónico de la cocina argentina, cuyo sabor intenso y especiado a base de hierbas y vinagre realza el sabor de la carne asada.'],
    ['q' => 'Generalmente, ¿qué tipo de vino es más recomendado para acompañar cortes de carne roja asada con alto contenido de grasa, como es común en un asado argentino?', 'choices' => ['Vino blanco seco', 'Vino rosado', 'Vino tinto de cuerpo completo', 'Vino espumoso'], 'answer' => 2, 'explanation' => 'Los vinos tintos de cuerpo completo tienen una alta concentración de taninos y astringencia, lo que ayuda a cortar y equilibrar la riqueza de las grasas presentes en la carne roja asada, limpiando el paladar.'],
	['q' => '¿Qué provincia es famosa por la especialidad llamada "Carbonada en zapallo" o "Carbonada en calabaza", donde el guiso se sirve dentro del propio vegetal ahuecado?', 'choices' => ['Mendoza', 'Córdoba', 'San Juan', 'Salta'], 'answer' => 1, 'explanation' => 'Aunque se consume en varias provincias, la Carbonada en calabaza es un plato tradicionalmente asociado a la cocina cuyana y del noroeste argentino, siendo muy popular en Mendoza y San Juan.'],
    ['q' => '¿Cuál es el ingrediente principal de la "Fainá"?', 'choices' => ['Harina de trigo', 'Sémola de maíz', 'Harina de garbanzos', 'Harina de arroz'], 'answer' => 2, 'explanation' => 'La Fainá es una adaptación de la "farinata" genovesa. Se elabora principalmente con harina de garbanzos, agua, aceite y sal, y se hornea hasta obtener una masa densa y sabrosa.'],
	['q' => '¿Qué ingrediente es la característica principal que diferencia a la Empanada Salteña de otras empanadas del país, como las tucumanas?', 'choices' => ['Carne cortada a cuchillo', 'Ají picante fuerte', 'Papa en cubos', 'Huevo duro picado'], 'answer' => 2, 'explanation' => 'Las empanadas de Salta son conocidas por incluir cubos de papa (patata) hervida en su relleno. Aunque el huevo duro y la carne a cuchillo son comunes en muchas empanadas, la papa es el sello distintivo de la receta salteña.'],
	['q' => '¿Qué ingrediente no puede faltar en la preparación del Locro, el guiso patrio tradicional de Argentina?', 'choices' => ['Arroz', 'Garbanzos', 'Maíz blanco pisado', 'Fideos secos'], 'answer' => 2, 'explanation' => 'El Locro es un guiso de cocción lenta que se elabora principalmente con maíz blanco pisado (o partido), porotos, carne y vísceras, siendo este cereal la base de su consistencia espesa.'],
	['q' => '¿Cuál de estas preparaciones NO es un tipo de pasta rellena popularmente consumida en Argentina?', 'choices' => ['Sorrentinos', 'Ravioles', 'Ñoquis', 'Canelones'], 'answer' => 2, 'explanation' => 'Los ñóquis (Gnocchi) son una pasta a base de papa y harina que se consume con salsa, pero no son considerados una pasta rellena; los ravioles, sorrentinos y canelones sí lo son.'],
	['q' => '¿Qué tipo de harina se utiliza principalmente para elaborar el Chipá, el pan de queso típico del noreste argentino?', 'choices' => ['Harina de trigo', 'Harina de maíz', 'Fécula de mandioca', 'Harina de arroz'], 'answer' => 2, 'explanation' => 'El Chipá se distingue por usar fécula o almidón de mandioca (yuca), lo que le da su textura elástica y sin gluten, en lugar de harina de trigo o maíz.'],
	['q' => 'El postre conocido como "Vigilante" o "Martín Fierro" combina queso (típicamente de postre) con un dulce. ¿Cuál es ese dulce tradicional?', 'choices' => ['Dulce de leche', 'Dulce de membrillo o batata', 'Mousse de chocolate', 'Jalea de pera'], 'answer' => 1, 'explanation' => 'El Vigilante es un postre sencillo y muy popular que consiste en una porción de queso fresco (o cremoso) junto a una porción de dulce de membrillo o de batata.'],
	['q' => '¿Cuál es el corte de carne de res más popular y tradicional para el asado a la parrilla en Argentina?', 'choices' => ['Lomo', 'Bife de chorizo', 'Vacío', 'Tira de asado'], 'answer' => 2, 'explanation' => 'Aunque todos son populares, el vacío, un corte de la parte baja del costillar, es históricamente uno de los más icónicos y consumidos en los asados argentinos por su sabor y textura fibrosa.'],
];


shuffle($questions);
$selected = array_slice($questions, 0, 25);

?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Trivia - MyFoods</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="../css/main.css">
	<link rel="stylesheet" href="../css/navbar.css">
	<link rel="icon" href="../img/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="../css/trivia.css">

</head>

<body>
	<?php include '../includes/navbar.php'; ?>

	<div class="container my-4">
		<img src="../img/image.png" class="trivia">

		<div id="triviaCard" class="">
			<div class="card-body">
				<div id="progress" class="mb-2 progress-boxes">
					<div class="pbox" id="box-question">
						<small>Pregunta</small>
						<div class="pvalue"><span id="currentNum">1</span> / <span id="totalNum">10</span></div>
					</div>
					<div class="pbox" id="box-lives">
						<small>Vidas</small>
						<div class="pvalue" id="lifes"> <!-- Hearts will be rendered here -->
							<!-- default fallback for non-JS: show 3 hearts -->
							♥ ♥ ♥
						</div>
					</div>
					<div class="pbox" id="box-score">
						<small>Puntuación</small>
						<div class="pvalue"><span id="scoreBox">0</span></div>
					</div>
				</div>
				<div class="question-seccion">
				<h5 id="questionText" class="question">Cargando pregunta...</h5>
				</div>
				<div id="choices" class=" mb-3 "></div>
				<div id="feedbackHeader" class="mb-1" style="min-height:1.5em; font-weight:700;"></div>
				<div id="feedbackExplanation" class="mb-3 text-muted" style="min-height:1.2em;"></div>

				<button id="nextBtn" class="buttono d-block mx-auto" disabled>Siguiente</button>
				<button id="restartBtn" class="buttono d-none ms-2">Reiniciar</button>
			</div>
		</div>
		<div id="result" class="mt-4 d-none">
			<h4>¡Resultado!</h4>
			<div id="afterScore" class="card p-3">
				<p>Tu puntaje: <strong id="finalScore">0</strong> / <span id="finalTotal">10</span></p>
				<div id="nameEntry">
					<label for="playerName">Escribe tu nombre para guardar el puntaje:</label>
					<div style="display:flex;gap:8px;margin-top:6px;">
						<input id="playerName" class="form-control" placeholder="Tu nombre" />
						<button id="saveScoreBtn" class="buttono">Guardar</button>
					</div>
				</div>
				<div id="leaderboardSection" class="d-none" style="margin-top:12px;">
					<h5>Tabla de puntuaciones</h5>
					<p id="rankText" style="font-weight:700;"></p>
					<div style="overflow:auto;">
						<table id="leaderboard" class="table table-sm">
							<thead>
								<tr><th>#</th><th>Nombre</th><th>Puntaje</th><th>Fecha</th></tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
					<button id="playAgainBtn" class="buttono mt-2">Jugar otra vez</button>
				</div>
			</div>
		</div>
	</div>

	<script>

	const QUESTIONS = <?php echo json_encode($selected, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE); ?>;
	const total = QUESTIONS.length;


	const lifes = document.getElementById('lifes');
	const scoreBox = document.getElementById('scoreBox');
	const questionText = document.getElementById('questionText');
	const choicesDiv = document.getElementById('choices');
	const feedbackHeader = document.getElementById('feedbackHeader');
	const feedbackExplanation = document.getElementById('feedbackExplanation');
	const nextBtn = document.getElementById('nextBtn');
	const restartBtn = document.getElementById('restartBtn');
	const currentNum = document.getElementById('currentNum');
	const totalNum = document.getElementById('totalNum');
	const resultDiv = document.getElementById('result');
	const scoreSpan = document.getElementById('score');

	let index = 0;
	let score = 0;
	let lives = 3;
	const maxLives = lives;
	let answered = false;

	totalNum.textContent = total;

		function renderQuestion() {
			if(lives <=  0){
				showResult();
				
			}
			const q = QUESTIONS[index];
			updateLivesDisplay();
			scoreBox.textContent = score;
			currentNum.textContent = index + 1;
			questionText.textContent = q.q;
			choicesDiv.innerHTML = '';
			feedbackHeader.textContent = '';
			feedbackHeader.className = '';
			feedbackExplanation.textContent = '';
			nextBtn.disabled = true;
			answered = false;

			q.choices.forEach((choice, i) => {
				const btn = document.createElement('button');
				btn.className = 'list-group-item list-group-item-action buttonw';
				btn.type = 'button';
				btn.textContent = choice;
				btn.dataset.index = i;
				btn.addEventListener('click', () => selectAnswer(i, btn));
				choicesDiv.appendChild(btn);
			});
		}

		function selectAnswer(choiceIndex, buttonEl) {
			if (answered) return; 
			answered = true;
			const correctIndex = QUESTIONS[index].answer;

			Array.from(choicesDiv.children).forEach(btn => {
				btn.classList.add('disabled');
				btn.disabled = true;
				const idx = Number(btn.dataset.index);
				if (idx === correctIndex) {
					btn.classList.add('list-group-item-success');
				}
				if (idx === choiceIndex && idx !== correctIndex) {
					btn.classList.add('list-group-item-danger');
					lives = lives - 1;
					updateLivesDisplay();
				}
			});

			if (choiceIndex === correctIndex) {
				feedbackHeader.textContent = 'Correcto';
				feedbackHeader.className = 'text-success';
				score++;
				scoreBox.textContent = score;
			} else {
				feedbackHeader.textContent = 'Incorrecto';
				feedbackHeader.className = 'text-danger';
			}


			feedbackExplanation.textContent = QUESTIONS[index].explanation || '';

			nextBtn.disabled = false;

			if (index === total - 1) {
				nextBtn.textContent = 'Ver resultado';
			} else {
				nextBtn.textContent = 'Siguiente';
			}
		}

		nextBtn.addEventListener('click', () => {
			if (!answered) return; 
			index++;
			if (index >= total) {
				showResult();
			} else {
				renderQuestion();
			}
		});

		restartBtn.addEventListener('click', () => {
			window.location.reload();
		});

		function showResult() {
			// Preparar y mostrar la pantalla de resultado
			document.getElementById('triviaCard').classList.add('d-none');
			resultDiv.classList.remove('d-none');
			// mostrar el puntaje final y pedir nombre
			document.getElementById('finalScore').textContent = score;
			document.getElementById('finalTotal').textContent = total;
			// mostrar formulario de nombre
			document.getElementById('nameEntry').classList.remove('d-none');
			document.getElementById('leaderboardSection').classList.add('d-none');
			// mostrar botón reiniciar oculto hasta que guarde o quiera jugar otra vez
			restartBtn.classList.remove('d-none');

			// Hook botones
			const saveBtn = document.getElementById('saveScoreBtn');
			const playAgainBtn = document.getElementById('playAgainBtn');
			saveBtn.onclick = handleSaveScore;
			playAgainBtn.onclick = () => { window.location.reload(); };
		}

		// Leaderboard helpers (localStorage)
		const LB_KEY = 'triviaLeaderboard_v1';

		function getLeaderboard() {
			try {
				const raw = localStorage.getItem(LB_KEY);
				return raw ? JSON.parse(raw) : [];
			} catch (e) { return []; }
		}

		function saveLeaderboard(arr) {
			localStorage.setItem(LB_KEY, JSON.stringify(arr));
		}

		function handleSaveScore() {
			const nameInput = document.getElementById('playerName');
			let name = (nameInput && nameInput.value) ? nameInput.value.trim() : '';
			if (!name) name = 'Anónimo';
			const board = getLeaderboard();
			const entry = { name: name, score: score, ts: Date.now() };
			board.push(entry);
			// ordenar desc por score, luego asc por timestamp
			board.sort((a,b) => (b.score - a.score) || (a.ts - b.ts));
			saveLeaderboard(board);
			renderLeaderboard(board);
			// mostrar sección
			document.getElementById('nameEntry').classList.add('d-none');
			document.getElementById('leaderboardSection').classList.remove('d-none');
		}

		function renderLeaderboard(board) {
			const tbody = document.querySelector('#leaderboard tbody');
			tbody.innerHTML = '';
			const topN = 10;
			for (let i = 0; i < Math.min(board.length, topN); i++) {
				const r = board[i];
				const tr = document.createElement('tr');
				tr.innerHTML = `<td>${i+1}</td><td>${escapeHtml(r.name)}</td><td>${r.score}</td><td>${new Date(r.ts).toLocaleString()}</td>`;
				tbody.appendChild(tr);
			}

			// Calcular en qué posición quedó la última entrada con el mismo timestamp (el más reciente con ese score)
			// Buscamos la primera entrada con score === score y ts equal to the one we just saved — but we don't pass ts here.
			// En su lugar, asumimos que la entrada más reciente con ese score es la del usuario: buscar índice del primer match por score y name 'Anónimo' o matching timestamp no posible aquí.
			// Mejor: buscar la primera entrada cuyo score === score y ts >= (Date.now()-2000)
			const now = Date.now();
			let rank = board.findIndex(e => e.score === score && (now - e.ts) < 60000);
			if (rank === -1) {
				// fallback: posición del primer con mismo score
				rank = board.findIndex(e => e.score === score);
			}
			if (rank === -1) {
				rank = board.findIndex(e => e.score < score);
				if (rank === -1) rank = board.length - 1;
			}
			const place = rank + 1;
			document.getElementById('rankText').textContent = `Has quedado en el puesto #${place} de ${board.length}`;
		}

		function escapeHtml(s) {
			return String(s).replace(/[&<>"']/g, function (m) { return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;"}[m]; });
		}

		function updateLivesDisplay() {
			if (!lifes) return;
			lifes.innerHTML = '';
			for (let i = 0; i < maxLives; i++) {
				const s = document.createElement('span');
				if (i < lives) {
					s.className = 'heart';
					s.textContent = '♥';
				} else {
					s.className = 'heart empty';
					s.textContent = '♡';
				}
				lifes.appendChild(s);
			}
		}

		renderQuestion();
	</script>
    
</body>

</html>