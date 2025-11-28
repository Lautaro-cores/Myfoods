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
	['q' => '¿Cómo se llama la técnica de cocinar a baja temperatura en un baño de agua controlado?', 'choices' => ['Saltear', 'Brasear', 'Sous-vide', 'Escalfar'], 'answer' => 2, 'explanation' => 'Sous-vide es cocinar al vacío a temperatura constante para lograr cocción uniforme y jugosa.'],
	['q' => '¿Cuál es el principal cereal usado para hacer risotto?', 'choices' => ['Basmati', 'Arborio', 'Cebada', 'Trigo'], 'answer' => 1, 'explanation' => 'El arroz Arborio tiene alto contenido de almidón, lo que da la cremosidad característica del risotto.'],
	['q' => '¿Qué fruta se utiliza para hacer la tradicional tarta francesa Tarte Tatin?', 'choices' => ['Peras', 'Manzanas', 'Ciruelas', 'Duraznos'], 'answer' => 1, 'explanation' => 'La Tarte Tatin clásica se hace con manzanas caramelizadas y cocidas boca abajo.'],
	['q' => '¿Qué tipo de queso es típico en la pizza napolitana?', 'choices' => ['Parmesano', 'Gouda', 'Mozzarella', 'Cheddar'], 'answer' => 2, 'explanation' => 'La mozzarella (fresca) es la más usada en la pizza napolitana por su textura y fundido.'],
	['q' => '¿Cuál es el nombre de la pasta japonesa hecha de harina de arroz y agua?', 'choices' => ['Soba', 'Udon', 'Gyoza', 'Mochi'], 'answer' => 3, 'explanation' => 'El mochi es una preparación hecha con harina de arroz, normalmente asociada a dulces japoneses.'],
	['q' => '¿Qué especia le da color amarillo al curry y muchas preparaciones?', 'choices' => ['Canela', 'Pimentón', 'Cúrcuma', 'Cardamomo'], 'answer' => 2, 'explanation' => 'La cúrcuma es la especia que aporta el color amarillo y un sabor terroso a muchos curries.'],
	['q' => '¿Cuál de estos métodos NO es una forma de conservar alimentos?', 'choices' => ['Fermentación', 'Encapsulado', 'Salazón', 'Deshidratación'], 'answer' => 1, 'explanation' => 'El encapsulado no es un método tradicional de conservación; fermentación, salazón y deshidratación sí lo son.'],
	['q' => '¿Qué corte de carne es conocido como ideal para estofados por su contenido de colágeno?', 'choices' => ['Lomo', 'Pecho (brisket)', 'Solomillo', 'Filete'], 'answer' => 1, 'explanation' => 'El pecho (brisket) tiene tejido conectivo que, con cocción larga, se transforma en colágeno gelificado, dando sabor y textura.'],
	['q' => '¿Cuál es el ingrediente principal del hummus?', 'choices' => ['Lentejas', 'Garbanzos', 'Frijoles', 'Maíz'], 'answer' => 1, 'explanation' => 'El hummus se hace con garbanzos cocidos y triturados, aliñados con tahini y limón.'],
	['q' => '¿Qué tipo de azúcar se usa habitualmente para caramelizar en recetas?', 'choices' => ['Glucosa', 'Azúcar moreno', 'Azúcar granulada', 'Azúcar invertido'], 'answer' => 2, 'explanation' => 'El azúcar granulada (blanca) se carameliza fácilmente al calentarse y es la más común para caramelo.'],
	['q' => '¿Cómo se llama la sopa fría tradicional de España hecha con tomate?', 'choices' => ['Gazpacho', 'Minestrone', 'Ratatouille', 'Borscht'], 'answer' => 0, 'explanation' => 'El gazpacho es una sopa fría a base de tomate, pimiento y pepino, típica del sur de España.'],
	['q' => '¿Qué tipo de aceite es más recomendado para freír por su punto de humo alto?', 'choices' => ['Aceite de oliva virgen extra', 'Aceite de linaza', 'Aceite de girasol alto oleico', 'Mantequilla'], 'answer' => 2, 'explanation' => 'El aceite de girasol alto oleico tiene un punto de humo elevado y estabilidad para frituras.'],
	['q' => '¿Qué levadura se usa para hacer pan tradicionalmente?', 'choices' => ['Levadura química', 'Bicarbonato', 'Levadura fresca (levadura de pan)', 'Polvo de hornear'], 'answer' => 2, 'explanation' => 'La levadura fresca o seca de pan (Saccharomyces cerevisiae) se usa para fermentar y leudar panes.'],
	['q' => '¿Cuál es el nombre del corte de pescado ideal para sashimi?', 'choices' => ['Atún', 'Bacalao', 'Salmón ahumado', 'Anchoa'], 'answer' => 0, 'explanation' => 'El atún (y también el salmón fresco) es popular para sashimi por su textura y sabor; aquí la opción correcta es atún.'],
	['q' => '¿Qué técnica se usa para cortar verduras en tiras muy finas (juliana)?', 'choices' => ['Brunoise', 'Macédoine', 'Juliana', 'Chiffonade'], 'answer' => 2, 'explanation' => 'La juliana es cortar en tiras finas; brunoise es cubos pequeños y chiffonade para hojas.'],
	['q' => '¿Qué elemento se añade a una mayonesa para emulsionarla?', 'choices' => ['Agua caliente', 'Aceite solo', 'Yema de huevo', 'Harina'], 'answer' => 2, 'explanation' => 'La yema de huevo contiene lecitina, un emulsionante natural que ayuda a formar la mayonesa.'],
	['q' => '¿Cuál es el ingrediente principal del tabulé?', 'choices' => ['Quinoa', 'Cuscús', 'Bulgur (trigo partido)', 'Arroz'], 'answer' => 2, 'explanation' => 'El tabulé tradicional se hace con bulgur (trigo partido), perejil, menta y limón.'],
];

// Mezclar preguntas y tomar las primeras 10 (aleatorias sin repetición)
shuffle($questions);
$selected = array_slice($questions, 0, 10);

?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Trivia - MyFoods</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="../css/main.css">
	<link rel="icon" href="../img/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="../css/trivia.css">

</head>

<body>
	<?php include '../includes/navbar.php'; ?>

	<div class="container my-4">
		<img src="../img/image.png" class="trivia">

		<div id="triviaCard" class="">
			<div class="card-body">
				<div id="progress" class="mb-2">Pregunta <span id="currentNum">1</span> / <span id="totalNum">10</span></div>
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
			<h4>Resultado</h4>
			<p>Puntaje: <span id="score">0</span> / 10</p>
		</div>
	</div>

	<script>
	// Preguntas pasadas desde PHP
	const QUESTIONS = <?php echo json_encode($selected, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE); ?>;
	const total = QUESTIONS.length;

	// Elementos
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
	let answered = false;

	totalNum.textContent = total;

		function renderQuestion() {
			if(lives <=  0){
				showResult();
				
			}
			const q = QUESTIONS[index];
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
			if (answered) return; // evitar doble click
			answered = true;
			const correctIndex = QUESTIONS[index].answer;
			// marcar botones
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
				}
			});

			if (choiceIndex === correctIndex) {
				feedbackHeader.textContent = 'Correcto';
				feedbackHeader.className = 'text-success';
				score++;
			} else {
				feedbackHeader.textContent = 'Incorrecto';
				feedbackHeader.className = 'text-danger';
			}

			// Mostrar siempre la explicación debajo del encabezado
			feedbackExplanation.textContent = QUESTIONS[index].explanation || '';

			nextBtn.disabled = false;
			// Si fue la última pregunta, cambiar texto del botón
			if (index === total - 1) {
				nextBtn.textContent = 'Ver resultado';
			} else {
				nextBtn.textContent = 'Siguiente';
			}
		}

		nextBtn.addEventListener('click', () => {
			if (!answered) return; // protección extra
			index++;
			if (index >= total) {
				showResult();
			} else {
				renderQuestion();
			}
		});

		restartBtn.addEventListener('click', () => {
			// recargar la página para reshufflear preguntas
			window.location.reload();
		});

		function showResult() {
			document.getElementById('triviaCard').classList.add('d-none');
			resultDiv.classList.remove('d-none');
			scoreSpan.textContent = score;
			restartBtn.classList.remove('d-none');
		}

		// Iniciar
		renderQuestion();
	</script>
    
</body>

</html>