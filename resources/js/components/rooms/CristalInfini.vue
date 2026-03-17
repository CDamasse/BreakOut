<template>
    <div class="cristal-game">
        <div class="cristal-game-header">
            <div class="cristal-game-title-block">
                <div class="cristal-gem-icon">💎</div>
                <h1>La Quête du Cristal d'Infini</h1>
            </div>
            <div class="cristal-timer" v-if="started && scene !== 'victory'">
                <i class="far fa-clock"></i> {{ formattedTime }}
            </div>
        </div>

        <!-- Progression -->
        <div class="cristal-progress" v-if="started && scene !== 'intro' && scene !== 'victory'">
            <div class="progress-step" :class="{ done: sceneIndex >= 1, active: sceneIndex === 1 }">
                <span>1</span> L'Antichambre
            </div>
            <div class="progress-sep"></div>
            <div class="progress-step" :class="{ done: sceneIndex >= 2, active: sceneIndex === 2 }">
                <span>2</span> La Bibliothèque
            </div>
            <div class="progress-sep"></div>
            <div class="progress-step" :class="{ done: sceneIndex >= 3 }">
                <span>🏆</span> Le Cristal
            </div>
        </div>

        <!-- Intro -->
        <div v-if="scene === 'intro'" class="cristal-scene intro-scene">
            <div class="intro-content">
                <div class="intro-gem-anim">💎</div>
                <h2>Prologue</h2>
                <p class="intro-text">
                    Vous êtes un aventurier légendaire. On raconte qu'au cœur d'un temple oublié
                    repose le <strong>Cristal d'Infini</strong> — une gemme capable d'exaucer n'importe
                    quel souhait. Après des années de recherche, vous avez enfin trouvé l'entrée du temple...
                </p>
                <p class="intro-text">
                    Deux épreuves vous séparent du cristal. Chaque salle renferme une énigme.
                    Observez bien votre environnement, chaque détail compte.
                </p>
                <div class="intro-rules">
                    <div class="rule"><i class="fas fa-eye"></i> Lisez attentivement les descriptions</div>
                    <div class="rule"><i class="fas fa-brain"></i> Analysez chaque indice</div>
                    <div class="rule"><i class="fas fa-gem"></i> Trouvez le cristal</div>
                </div>
                <button class="btn-cristal" @click="startGame">
                    <i class="fas fa-play"></i> Commencer l'aventure
                </button>
            </div>
        </div>

        <!-- Scène 1 : L'Antichambre -->
        <div v-if="scene === 'scene1'" class="cristal-scene">
            <div class="scene-header">
                <h2><i class="fas fa-dungeon"></i> L'Antichambre du Temple</h2>
            </div>
            <div class="scene-body">
                <div class="scene-description">
                    <p>
                        Vous pénétrez dans une vaste salle circulaire. Le silence est total,
                        seulement brisé par le crépitement de <strong class="clue">trois flambeaux</strong>
                        qui brûlent à votre droite.
                    </p>
                    <p>
                        Au centre, une statue imposante à <strong class="clue">sept têtes</strong> de dragon
                        vous observe de ses yeux de pierre. Autour de son socle, vous distinguez
                        <strong class="clue">deux symboles</strong> anciens gravés en spirale.
                    </p>
                    <p>
                        La porte en pierre qui bloque votre avancée est fermée par un cadenas à quatre
                        chiffres. Tout autour de vous, <strong class="clue">quatre piliers</strong> colossaux
                        soutiennent le plafond voûté. À leur base, une inscription :
                    </p>
                    <blockquote class="scene-quote">
                        <i class="fas fa-quote-left"></i>
                        <em>« Le code est dans ce que vous voyez. »</em>
                        <i class="fas fa-quote-right"></i>
                    </blockquote>
                </div>

                <div class="puzzle-area">
                    <label class="puzzle-label">
                        <i class="fas fa-lock"></i> Entrez le code à 4 chiffres :
                    </label>
                    <div class="puzzle-input-group">
                        <input
                            type="text"
                            maxlength="4"
                            v-model="input1"
                            placeholder="_ _ _ _"
                            class="puzzle-input"
                            @keyup.enter="checkScene1"
                        >
                        <button class="btn-cristal btn-validate" @click="checkScene1">
                            <i class="fas fa-key"></i> Valider
                        </button>
                    </div>
                    <div class="error-msg" v-if="error1">
                        <i class="fas fa-times-circle"></i> {{ error1 }}
                    </div>
                    <div class="tries-msg" v-if="tries1 > 0">
                        Tentatives : {{ tries1 }} / 5
                        <span v-if="tries1 >= 3" class="hint-unlock">
                            — <a href="#" @click.prevent="showHint1 = !showHint1">Voir un indice</a>
                        </span>
                    </div>
                    <div class="hint-box" v-if="showHint1">
                        <i class="fas fa-lightbulb"></i>
                        Lisez les éléments dans l'ordre où ils apparaissent dans la description.
                        Comptez chacun des éléments mis en évidence.
                    </div>
                </div>
            </div>
        </div>

        <!-- Scène 2 : La Bibliothèque -->
        <div v-if="scene === 'scene2'" class="cristal-scene">
            <div class="scene-header">
                <h2><i class="fas fa-book"></i> La Bibliothèque Secrète</h2>
            </div>
            <div class="scene-inventory">
                <span><i class="fas fa-key"></i> Vous avez trouvé : <strong>Clé de la bibliothèque</strong></span>
            </div>
            <div class="scene-body">
                <div class="scene-description">
                    <p>
                        La lourde porte s'ouvre dans un grondement. Une bibliothèque plongée dans une
                        lumière dorée s'étend devant vous. Des milliers de parchemins couvrent les murs
                        du sol au plafond. Des torches en cristal projettent des reflets chatoyants sur
                        les reliures anciennes.
                    </p>
                    <p>
                        Au centre de la salle, un pupitre en bois sombre présente un livre ouvert.
                        Une main invisible semble avoir marqué la page. Vous vous approchez et lisez :
                    </p>
                    <div class="riddle-book">
                        <div class="riddle-header">
                            <i class="fas fa-scroll"></i> Énigme du Gardien
                        </div>
                        <div class="riddle-content">
                            <p>
                                <em>
                                    « Je suis invisible mais je guide les yeux,<br>
                                    Je naît d'une flamme et m'éteins dans les cieux.<br>
                                    Les aveugles me cherchent, les sages me trouvent,<br>
                                    Sans moi, le cristal dans les ténèbres demeure.<br>
                                    <br>
                                    <strong>Que suis-je ?</strong> »
                                </em>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="puzzle-area">
                    <label class="puzzle-label">
                        <i class="fas fa-feather-alt"></i> Votre réponse :
                    </label>
                    <div class="puzzle-input-group">
                        <input
                            type="text"
                            v-model="input2"
                            placeholder="Entrez votre réponse..."
                            class="puzzle-input"
                            @keyup.enter="checkScene2"
                        >
                        <button class="btn-cristal btn-validate" @click="checkScene2">
                            <i class="fas fa-check"></i> Valider
                        </button>
                    </div>
                    <div class="error-msg" v-if="error2">
                        <i class="fas fa-times-circle"></i> {{ error2 }}
                    </div>
                    <div class="tries-msg" v-if="tries2 > 0">
                        Tentatives : {{ tries2 }} / 5
                        <span v-if="tries2 >= 3" class="hint-unlock">
                            — <a href="#" @click.prevent="showHint2 = !showHint2">Voir un indice</a>
                        </span>
                    </div>
                    <div class="hint-box" v-if="showHint2">
                        <i class="fas fa-lightbulb"></i>
                        Il s'agit d'un phénomène naturel lié aux flammes. Sans lui, on ne voit rien.
                        Répondez en français, une seule syllabe.
                    </div>
                </div>
            </div>
        </div>

        <!-- Victoire -->
        <div v-if="scene === 'victory'" class="cristal-scene victory-scene">
            <div class="victory-content">
                <div class="victory-gem-anim">💎</div>
                <h2>Le Cristal d'Infini est à vous !</h2>
                <p class="victory-text">
                    Une lumière aveuglante inonde la chambre. Les murs s'écartent, révélant
                    une alcôve secrète. Le <strong>Cristal d'Infini</strong> flotte doucement,
                    baigné d'une lueur dorée et pulsante. Vous tendez la main...
                    il est à vous, aventurier !
                </p>
                <div class="victory-stats">
                    <div class="stat-block">
                        <i class="far fa-clock"></i>
                        <span>Temps</span>
                        <strong>{{ formattedTime }}</strong>
                    </div>
                    <div class="stat-block">
                        <i class="fas fa-trophy"></i>
                        <span>Statut</span>
                        <strong>Victorieux !</strong>
                    </div>
                    <div class="stat-block">
                        <i class="fas fa-redo"></i>
                        <span>Essais</span>
                        <strong>{{ tries1 + tries2 }} tentative(s)</strong>
                    </div>
                </div>
                <div class="victory-buttons">
                    <button class="btn-cristal" @click="resetGame">
                        <i class="fas fa-redo"></i> Recommencer
                    </button>
                    <router-link to="/rooms" class="btn-cristal btn-secondary-cristal">
                        <i class="fas fa-door-open"></i> Autres salles
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "CristalInfini",
    data() {
        return {
            scene: 'intro',
            started: false,
            sceneIndex: 0,
            // Timer
            timerInterval: null,
            seconds: 0,
            // Scene 1
            input1: '',
            error1: '',
            tries1: 0,
            showHint1: false,
            // Scene 2
            input2: '',
            error2: '',
            tries2: 0,
            showHint2: false,
        }
    },
    computed: {
        formattedTime() {
            const m = Math.floor(this.seconds / 60).toString().padStart(2, '0');
            const s = (this.seconds % 60).toString().padStart(2, '0');
            return `${m}:${s}`;
        }
    },
    methods: {
        startGame() {
            this.scene = 'scene1';
            this.sceneIndex = 1;
            this.started = true;
            this.seconds = 0;
            this.timerInterval = setInterval(() => { this.seconds++; }, 1000);
        },
        checkScene1() {
            this.error1 = '';
            const answer = this.input1.trim();
            if (answer === '3724') {
                this.scene = 'scene2';
                this.sceneIndex = 2;
                this.input1 = '';
            } else {
                this.tries1++;
                this.error1 = 'Code incorrect. Comptez bien chaque élément visible dans la salle.';
            }
        },
        checkScene2() {
            this.error2 = '';
            const answer = this.input2.trim().toUpperCase()
                .normalize("NFD").replace(/[\u0300-\u036f]/g, "");
            const valid = ['LUMIERE', 'LA LUMIERE', 'FEU', 'LE FEU', 'LUX'];
            if (valid.includes(answer)) {
                clearInterval(this.timerInterval);
                this.scene = 'victory';
                this.sceneIndex = 3;
                this.input2 = '';
            } else {
                this.tries2++;
                this.error2 = 'Réponse incorrecte. Relisez attentivement l\'énigme.';
            }
        },
        resetGame() {
            clearInterval(this.timerInterval);
            this.scene = 'intro';
            this.started = false;
            this.sceneIndex = 0;
            this.seconds = 0;
            this.input1 = '';
            this.input2 = '';
            this.error1 = '';
            this.error2 = '';
            this.tries1 = 0;
            this.tries2 = 0;
            this.showHint1 = false;
            this.showHint2 = false;
        }
    },
    beforeDestroy() {
        clearInterval(this.timerInterval);
    }
}
</script>

<style scoped>
/* ---- Conteneur principal ---- */
.cristal-game {
    min-height: 100vh;
    background: linear-gradient(135deg, #0a0a1a 0%, #1a0a2e 50%, #0d0d1f 100%);
    color: #e8e0f0;
    font-family: 'Georgia', serif;
    padding-bottom: 60px;
}

/* ---- Header ---- */
.cristal-game-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 30px;
    background: rgba(0,0,0,0.4);
    border-bottom: 2px solid #6a35a8;
}

.cristal-game-title-block {
    display: flex;
    align-items: center;
    gap: 12px;
}

.cristal-gem-icon {
    font-size: 32px;
    animation: glow-pulse 2s ease-in-out infinite;
}

.cristal-game-header h1 {
    margin: 0;
    font-size: 1.6rem;
    color: #c9a8ff;
    text-shadow: 0 0 20px rgba(138, 43, 226, 0.7);
}

.cristal-timer {
    background: rgba(106, 53, 168, 0.3);
    border: 1px solid #6a35a8;
    border-radius: 8px;
    padding: 8px 16px;
    font-size: 1.1rem;
    color: #c9a8ff;
}

/* ---- Progression ---- */
.cristal-progress {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    padding: 16px 20px;
    background: rgba(0,0,0,0.3);
}

.progress-step {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.85rem;
    color: #888;
    border: 1px solid transparent;
    transition: all 0.3s;
}

.progress-step span {
    background: rgba(255,255,255,0.1);
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
}

.progress-step.active {
    color: #c9a8ff;
    border-color: #6a35a8;
    background: rgba(106, 53, 168, 0.2);
}

.progress-step.done {
    color: #7fff7f;
}

.progress-sep {
    width: 40px;
    height: 2px;
    background: rgba(106, 53, 168, 0.4);
    margin: 0 4px;
}

/* ---- Scènes ---- */
.cristal-scene {
    max-width: 800px;
    margin: 40px auto;
    padding: 0 20px;
}

.scene-header h2 {
    color: #c9a8ff;
    font-size: 1.5rem;
    border-bottom: 2px solid #6a35a8;
    padding-bottom: 12px;
    margin-bottom: 24px;
}

.scene-inventory {
    background: rgba(106, 53, 168, 0.15);
    border: 1px solid #6a35a8;
    border-radius: 8px;
    padding: 10px 16px;
    margin-bottom: 20px;
    font-size: 0.9rem;
    color: #c9a8ff;
}

.scene-description {
    background: rgba(255,255,255,0.04);
    border-left: 4px solid #6a35a8;
    border-radius: 0 8px 8px 0;
    padding: 20px 24px;
    margin-bottom: 30px;
    line-height: 1.8;
}

.scene-description p {
    margin-bottom: 14px;
}

.scene-description p:last-child {
    margin-bottom: 0;
}

.clue {
    color: #ffd700;
    background: rgba(255, 215, 0, 0.1);
    padding: 1px 4px;
    border-radius: 3px;
}

.scene-quote {
    margin: 16px 0 0;
    padding: 12px 16px;
    background: rgba(0,0,0,0.3);
    border: 1px solid #4a2580;
    border-radius: 8px;
    font-style: italic;
    color: #c9a8ff;
    text-align: center;
}

/* ---- Puzzle ---- */
.puzzle-area {
    background: rgba(0,0,0,0.3);
    border: 1px solid #4a2580;
    border-radius: 12px;
    padding: 24px;
}

.puzzle-label {
    display: block;
    font-size: 1rem;
    color: #c9a8ff;
    margin-bottom: 12px;
}

.puzzle-input-group {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.puzzle-input {
    flex: 1;
    min-width: 180px;
    background: rgba(255,255,255,0.05);
    border: 2px solid #6a35a8;
    border-radius: 8px;
    color: #e8e0f0;
    padding: 10px 16px;
    font-size: 1.1rem;
    letter-spacing: 4px;
    outline: none;
    transition: border-color 0.2s;
}

.puzzle-input:focus {
    border-color: #c9a8ff;
}

.puzzle-input::placeholder {
    color: #555;
    letter-spacing: 2px;
}

.error-msg {
    color: #ff6b6b;
    margin-top: 10px;
    font-size: 0.9rem;
}

.tries-msg {
    margin-top: 8px;
    font-size: 0.85rem;
    color: #888;
}

.hint-unlock a {
    color: #c9a8ff;
    text-decoration: underline;
    cursor: pointer;
}

.hint-box {
    margin-top: 12px;
    background: rgba(106, 53, 168, 0.2);
    border: 1px solid #6a35a8;
    border-radius: 8px;
    padding: 12px 16px;
    font-size: 0.9rem;
    color: #ffd700;
}

/* ---- Boutons ---- */
.btn-cristal {
    background: linear-gradient(135deg, #6a35a8, #4a2580);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 10px 24px;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-cristal:hover {
    background: linear-gradient(135deg, #8a55c8, #6a35a8);
    transform: translateY(-1px);
    box-shadow: 0 4px 20px rgba(106, 53, 168, 0.5);
    color: #fff;
}

.btn-secondary-cristal {
    background: rgba(255,255,255,0.1);
    border: 1px solid #6a35a8;
}

.btn-secondary-cristal:hover {
    background: rgba(255,255,255,0.15);
}

.btn-validate {
    white-space: nowrap;
}

/* ---- Intro ---- */
.intro-scene {
    text-align: center;
}

.intro-content {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
}

.intro-gem-anim {
    font-size: 80px;
    animation: glow-pulse 2.5s ease-in-out infinite;
    display: block;
    margin-bottom: 20px;
}

.intro-content h2 {
    color: #c9a8ff;
    font-size: 2rem;
    margin-bottom: 20px;
}

.intro-text {
    font-size: 1.05rem;
    line-height: 1.8;
    margin-bottom: 16px;
    color: #ccc;
}

.intro-rules {
    display: flex;
    justify-content: center;
    gap: 24px;
    flex-wrap: wrap;
    margin: 28px 0;
}

.rule {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #c9a8ff;
    font-size: 0.95rem;
}

/* ---- Riddle Book ---- */
.riddle-book {
    margin: 20px 0;
    background: rgba(20, 10, 40, 0.8);
    border: 2px solid #6a35a8;
    border-radius: 12px;
    overflow: hidden;
}

.riddle-header {
    background: rgba(106, 53, 168, 0.4);
    padding: 10px 20px;
    font-size: 0.95rem;
    color: #c9a8ff;
    letter-spacing: 1px;
}

.riddle-content {
    padding: 24px;
    font-size: 1.05rem;
    line-height: 2;
    color: #e8e0f0;
    text-align: center;
}

/* ---- Victory ---- */
.victory-scene {
    text-align: center;
}

.victory-content {
    max-width: 600px;
    margin: 0 auto;
}

.victory-gem-anim {
    font-size: 100px;
    animation: victory-bounce 1s ease-in-out infinite alternate;
    display: block;
    margin-bottom: 24px;
}

.victory-scene h2 {
    color: #ffd700;
    font-size: 2rem;
    text-shadow: 0 0 30px rgba(255, 215, 0, 0.7);
    margin-bottom: 20px;
}

.victory-text {
    font-size: 1.05rem;
    line-height: 1.8;
    color: #ccc;
    margin-bottom: 30px;
}

.victory-stats {
    display: flex;
    justify-content: center;
    gap: 24px;
    flex-wrap: wrap;
    margin-bottom: 30px;
}

.stat-block {
    background: rgba(106, 53, 168, 0.2);
    border: 1px solid #6a35a8;
    border-radius: 12px;
    padding: 16px 24px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    min-width: 120px;
}

.stat-block i {
    font-size: 1.4rem;
    color: #c9a8ff;
}

.stat-block span {
    font-size: 0.8rem;
    color: #888;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.stat-block strong {
    color: #ffd700;
    font-size: 1rem;
}

.victory-buttons {
    display: flex;
    justify-content: center;
    gap: 16px;
    flex-wrap: wrap;
}

/* ---- Animations ---- */
@keyframes glow-pulse {
    0%, 100% { filter: drop-shadow(0 0 8px rgba(138, 43, 226, 0.6)); }
    50% { filter: drop-shadow(0 0 24px rgba(138, 43, 226, 1)); }
}

@keyframes victory-bounce {
    0% { transform: translateY(0) scale(1); filter: drop-shadow(0 0 10px rgba(255,215,0,0.6)); }
    100% { transform: translateY(-15px) scale(1.05); filter: drop-shadow(0 0 30px rgba(255,215,0,1)); }
}

/* ---- Responsive ---- */
@media (max-width: 600px) {
    .cristal-game-header {
        flex-direction: column;
        gap: 10px;
        text-align: center;
    }
    .cristal-game-header h1 {
        font-size: 1.2rem;
    }
    .cristal-progress {
        flex-wrap: wrap;
        gap: 8px;
    }
    .progress-sep {
        display: none;
    }
    .puzzle-input-group {
        flex-direction: column;
    }
}
</style>
