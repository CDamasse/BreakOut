<template>
    <div class="cristal-game" ref="gameRoot">
        <!-- Header -->
        <div class="cristal-game-header">
            <div class="cristal-game-title-block">
                <span class="cristal-gem-icon">💎</span>
                <h1>La Quête du Cristal d'Infini</h1>
            </div>
            <div class="cristal-timer" v-if="gameState === 'maze' || gameState === 'symbol-puzzle'">
                <i class="far fa-clock"></i> {{ formattedTime }}
                <span v-if="timeLeft < 300" class="timer-danger"> ⚠️</span>
            </div>
        </div>

        <!-- ==================== INTRO ==================== -->
        <div v-if="gameState === 'intro'" class="cristal-scene intro-scene">
            <div class="intro-content">
                <div class="intro-gem-anim">💎</div>
                <h2>La Quête du Cristal d'Infini</h2>
                <p class="intro-text">
                    Sept cristaux élémentaires ont été dispersés dans un labyrinthe gardé par un terrible
                    <strong>Minotaure</strong>. Rassemblez-les tous pour forger le légendaire
                    <strong>Cristal d'Infini</strong> et ouvrir la porte des runes.
                </p>

                <div class="crystals-grid">
                    <div v-for="c in crystalTypes" :key="c.id" class="crystal-info">
                        <span class="crystal-dot" :style="{ background: c.color }"></span>
                        <span>{{ c.name }}</span>
                    </div>
                </div>

                <div class="intro-rules">
                    <div class="rule"><i class="fas fa-arrows-alt"></i> Déplacez-vous avec les flèches ou ZQSD</div>
                    <div class="rule"><i class="fas fa-hourglass-half"></i> 45 minutes pour compléter la quête</div>
                    <div class="rule"><i class="fas fa-skull"></i> Le Minotaure vous coûte 2 min par capture (3 = défaite)</div>
                </div>

                <button class="btn-cristal" @click="startGame">
                    <i class="fas fa-play"></i> Entrer dans le labyrinthe
                </button>
            </div>
        </div>

        <!-- ==================== MAZE ==================== -->
        <div v-if="gameState === 'maze'" class="maze-scene">

            <!-- HUD -->
            <div class="maze-hud">
                <div class="hud-crystals">
                    <span
                        v-for="c in crystalTypes"
                        :key="c.id"
                        class="crystal-hud-badge"
                        :class="{ collected: collectedCrystals.includes(c.id) }"
                        :title="c.name"
                    >
                        <span class="crystal-hud-dot" :style="{ background: c.color }"></span>
                    </span>
                    <span class="hud-count">{{ collectedCrystals.length }}/7</span>
                </div>
                <div class="hud-right">
                    <span v-if="minotaurWarning" class="hud-warning">⚠️ Minotaure proche !</span>
                    <span v-if="minotaurCatches > 0" class="hud-catches">💀 {{ minotaurCatches }}/3</span>
                </div>
            </div>

            <!-- Flash message -->
            <transition name="fade">
                <div v-if="flashMessage" class="flash-message" :class="'flash-' + flashType">
                    {{ flashMessage }}
                </div>
            </transition>

            <!-- Canvas -->
            <div class="canvas-wrapper">
                <canvas ref="mazeCanvas"></canvas>
            </div>

            <!-- Legend -->
            <div class="maze-legend">
                <span><span class="legend-dot" style="background:#c9a8ff">⬤</span> Vous</span>
                <span><span class="legend-dot" style="background:#8b0000">⬤</span> Minotaure</span>
                <span><span class="legend-dot" style="background:#ffd700">⬤</span> Sortie</span>
                <span><span class="legend-dot" style="background:#3aa3ff">⬤</span> Cristaux</span>
            </div>

            <!-- Mobile controls -->
            <div class="mobile-controls">
                <div class="mobile-row">
                    <button class="mobile-btn" @click="movePlayer('N')">↑</button>
                </div>
                <div class="mobile-row">
                    <button class="mobile-btn" @click="movePlayer('W')">←</button>
                    <button class="mobile-btn" @click="movePlayer('S')">↓</button>
                    <button class="mobile-btn" @click="movePlayer('E')">→</button>
                </div>
            </div>
        </div>

        <!-- ==================== SYMBOL PUZZLE ==================== -->
        <div v-if="gameState === 'symbol-puzzle'" class="cristal-scene">
            <div class="scene-header">
                <h2><i class="fas fa-door-open"></i> La Porte des Runes</h2>
            </div>
            <div class="scene-description">
                <p>
                    Vous avez rassemblé les <strong>7 cristaux élémentaires</strong> ! La porte de la sortie est gravée
                    de vingt runes anciennes. Quatre d'entre elles brillent de couleurs élémentaires.
                </p>
                <p>Activez-les dans le bon ordre en suivant la <strong>séquence des éléments primordiaux</strong> :</p>
                <div class="symbol-order-hint">
                    <span v-for="(colorKey, i) in symbolOrder" :key="i" class="symbol-hint-step">
                        <span class="symbol-hint-dot" :style="{ background: colorMap[colorKey] }"></span>
                        <span class="symbol-hint-name">{{ crystalNameMap[colorKey] }}</span>
                        <span v-if="i < symbolOrder.length - 1" class="symbol-hint-arrow">→</span>
                    </span>
                </div>
            </div>

            <div class="symbols-grid">
                <div
                    v-for="sym in symbols"
                    :key="sym.id"
                    class="rune-symbol"
                    :class="{
                        'rune-colored': sym.colorKey,
                        'rune-activated': sym.activated,
                        'rune-wrong': sym.wrong,
                        'rune-plain': !sym.colorKey
                    }"
                    :style="sym.colorKey ? {
                        borderColor: colorMap[sym.colorKey],
                        color: sym.activated ? '#fff' : colorMap[sym.colorKey],
                        background: sym.activated ? colorMap[sym.colorKey] + '44' : ''
                    } : {}"
                    @click="clickSymbol(sym)"
                >{{ sym.glyph }}</div>
            </div>

            <div class="error-msg" v-if="symbolError">
                <i class="fas fa-times-circle"></i> {{ symbolError }}
            </div>
            <div class="symbol-progress">
                <span v-for="(colorKey, i) in symbolOrder" :key="i"
                    class="progress-dot"
                    :class="{ filled: i < symbolClicked.length }"
                    :style="i < symbolClicked.length ? { background: colorMap[colorKey] } : {}"
                ></span>
                {{ symbolClicked.length }}/{{ symbolOrder.length }} runes activées
            </div>
        </div>

        <!-- ==================== VICTORY ==================== -->
        <div v-if="gameState === 'victory'" class="cristal-scene victory-scene">
            <div class="victory-content">
                <div class="victory-gem-anim">💎</div>
                <h2>Le Cristal d'Infini est à vous !</h2>
                <p class="victory-text">
                    Les sept cristaux élémentaires fusionnent dans un tourbillon de lumière.
                    Le <strong>Cristal d'Infini</strong> se matérialise devant vous, baigné d'une lueur dorée.
                    Vous avez accompli la quête, aventurier légendaire !
                </p>
                <div class="victory-stats">
                    <div class="stat-block">
                        <i class="far fa-clock"></i>
                        <span>Temps utilisé</span>
                        <strong>{{ elapsedFormatted }}</strong>
                    </div>
                    <div class="stat-block">
                        <i class="fas fa-gem"></i>
                        <span>Cristaux</span>
                        <strong>7/7</strong>
                    </div>
                    <div class="stat-block">
                        <i class="fas fa-skull"></i>
                        <span>Captures</span>
                        <strong>{{ minotaurCatches }}</strong>
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

        <!-- ==================== DEFEAT ==================== -->
        <div v-if="gameState === 'defeat'" class="cristal-scene defeat-scene">
            <div class="defeat-content">
                <div class="defeat-icon">💀</div>
                <h2>{{ defeatReason }}</h2>
                <p class="defeat-text">
                    Le labyrinthe garde encore ses secrets... Mais rien ne vous empêche de réessayer !
                </p>
                <div class="defeat-stats">
                    <span>Cristaux collectés : <strong>{{ collectedCrystals.length }}/7</strong></span>
                </div>
                <button class="btn-cristal" @click="resetGame">
                    <i class="fas fa-redo"></i> Réessayer
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "CristalInfini",
    data() {
        return {
            gameState: 'intro',

            // Maze parameters
            ROWS: 17,
            COLS: 17,
            CELL: 30,
            maze: [],

            // Positions
            player: { r: 0, c: 0 },
            exitPos: { r: 16, c: 16 },

            // Crystals
            crystalTypes: [
                { id: 'eau',     name: "Cristal d'eau",      color: '#3aa3ff' },
                { id: 'vide',    name: 'Cristal du vide',     color: '#aaaaaa' },
                { id: 'glaces',  name: 'Cristal des glaces',  color: '#7fffff' },
                { id: 'desert',  name: 'Cristal du désert',   color: '#ff9966' },
                { id: 'plaines', name: 'Cristal des plaines', color: '#5cb85c' },
                { id: 'feu',     name: 'Cristal du feu',      color: '#e74c3c' },
                { id: 'foudre',  name: 'Cristal de foudre',   color: '#f1c40f' },
            ],
            crystalPositions: [],
            collectedCrystals: [],

            // Minotaur
            minotaur: { r: 16, c: 0 },
            minotaurInterval: null,
            minotaurCatches: 0,
            minotaurWarning: false,

            // Timer (countdown from 45 min)
            timeLeft: 45 * 60,
            timerInterval: null,
            startTime: 0,

            // Flash
            flashMessage: '',
            flashType: 'info',
            flashTimeout: null,

            // Symbol puzzle
            symbols: [],
            symbolOrder: ['eau', 'feu', 'glaces', 'foudre'],
            symbolClicked: [],
            symbolError: '',
            colorMap: {
                eau:    '#3aa3ff',
                feu:    '#e74c3c',
                glaces: '#7fffff',
                foudre: '#f1c40f',
            },
            crystalNameMap: {
                eau:    "Eau",
                feu:    "Feu",
                glaces: "Glace",
                foudre: "Foudre",
            },

            // Render
            animFrame: null,
        };
    },

    computed: {
        formattedTime() {
            const m = Math.floor(this.timeLeft / 60).toString().padStart(2, '0');
            const s = (this.timeLeft % 60).toString().padStart(2, '0');
            return `${m}:${s}`;
        },
        elapsedFormatted() {
            const elapsed = 45 * 60 - this.timeLeft;
            const m = Math.floor(elapsed / 60).toString().padStart(2, '0');
            const s = (elapsed % 60).toString().padStart(2, '0');
            return `${m}:${s}`;
        },
        defeatReason() {
            if (this.timeLeft <= 0) return "Le temps est écoulé !";
            return "Le Minotaure vous a capturé 3 fois !";
        },
    },

    methods: {
        // ==============================
        // GAME LIFECYCLE
        // ==============================
        startGame() {
            this.generateMaze();
            this.placeCrystals();
            this.player = { r: 0, c: 0 };
            this.minotaur = { r: this.ROWS - 1, c: 0 };
            this.collectedCrystals = [];
            this.minotaurCatches = 0;
            this.minotaurWarning = false;
            this.timeLeft = 45 * 60;
            this.flashMessage = '';
            this.gameState = 'maze';

            this.$nextTick(() => {
                this.setupCanvas();
                this.startTimers();
                this.startRender();
            });
        },

        resetGame() {
            this.stopAll();
            this.symbols = [];
            this.symbolClicked = [];
            this.symbolError = '';
            this.collectedCrystals = [];
            this.minotaurCatches = 0;
            this.gameState = 'intro';
        },

        stopAll() {
            clearInterval(this.timerInterval);
            clearInterval(this.minotaurInterval);
            clearTimeout(this.flashTimeout);
            if (this.animFrame) cancelAnimationFrame(this.animFrame);
            this.timerInterval = null;
            this.minotaurInterval = null;
            this.animFrame = null;
        },

        // ==============================
        // MAZE GENERATION (Recursive Backtracker)
        // ==============================
        generateMaze() {
            // Init grid — all walls present
            const grid = [];
            for (let r = 0; r < this.ROWS; r++) {
                grid[r] = [];
                for (let c = 0; c < this.COLS; c++) {
                    grid[r][c] = { N: true, S: true, E: true, W: true, vis: false };
                }
            }
            this.maze = grid;

            const stack = [{ r: 0, c: 0 }];
            this.maze[0][0].vis = true;

            while (stack.length > 0) {
                const curr = stack[stack.length - 1];
                const unvisited = this.getUnvisitedNeighbors(curr);
                if (unvisited.length === 0) {
                    stack.pop();
                } else {
                    const next = unvisited[Math.floor(Math.random() * unvisited.length)];
                    this.carvePassage(curr, next);
                    this.maze[next.r][next.c].vis = true;
                    stack.push({ r: next.r, c: next.c });
                }
            }
        },

        getUnvisitedNeighbors({ r, c }) {
            const dirs = [
                { dr: -1, dc: 0 },
                { dr:  1, dc: 0 },
                { dr:  0, dc: -1 },
                { dr:  0, dc:  1 },
            ];
            return dirs
                .map(d => ({ r: r + d.dr, c: c + d.dc }))
                .filter(n =>
                    n.r >= 0 && n.r < this.ROWS &&
                    n.c >= 0 && n.c < this.COLS &&
                    !this.maze[n.r][n.c].vis
                );
        },

        carvePassage(from, to) {
            const dr = to.r - from.r;
            const dc = to.c - from.c;
            if (dr === -1) { this.maze[from.r][from.c].N = false; this.maze[to.r][to.c].S = false; }
            if (dr ===  1) { this.maze[from.r][from.c].S = false; this.maze[to.r][to.c].N = false; }
            if (dc === -1) { this.maze[from.r][from.c].W = false; this.maze[to.r][to.c].E = false; }
            if (dc ===  1) { this.maze[from.r][from.c].E = false; this.maze[to.r][to.c].W = false; }
        },

        // ==============================
        // CRYSTAL PLACEMENT
        // ==============================
        placeCrystals() {
            const occupied = new Set(['0,0', `${this.ROWS-1},${this.COLS-1}`, `${this.ROWS-1},0`]);
            const positions = [];

            for (const crystal of this.crystalTypes) {
                let r, c;
                do {
                    r = Math.floor(Math.random() * this.ROWS);
                    c = Math.floor(Math.random() * this.COLS);
                } while (occupied.has(`${r},${c}`));
                occupied.add(`${r},${c}`);
                positions.push({ r, c, id: crystal.id });
            }
            this.crystalPositions = positions;
        },

        // ==============================
        // PLAYER MOVEMENT
        // ==============================
        onKeyDown(e) {
            if (this.gameState !== 'maze') return;
            const keyMap = {
                'ArrowUp':    'N', 'z': 'N', 'Z': 'N',
                'ArrowDown':  'S', 's': 'S', 'S': 'S',
                'ArrowLeft':  'W', 'q': 'W', 'Q': 'W',
                'ArrowRight': 'E', 'd': 'E', 'D': 'E',
            };
            const dir = keyMap[e.key];
            if (dir) {
                e.preventDefault();
                this.movePlayer(dir);
            }
        },

        movePlayer(dir) {
            if (this.gameState !== 'maze' || !this.maze.length) return;
            const cell = this.maze[this.player.r][this.player.c];
            if (cell[dir]) return; // wall blocks

            const delta = { N: [-1, 0], S: [1, 0], W: [0, -1], E: [0, 1] };
            const [dr, dc] = delta[dir];
            const nr = this.player.r + dr;
            const nc = this.player.c + dc;

            if (nr < 0 || nr >= this.ROWS || nc < 0 || nc >= this.COLS) return;

            this.player = { r: nr, c: nc };
            this.checkCrystalCollection();
            this.checkMinotaurCatch();
            this.checkExit();
        },

        checkCrystalCollection() {
            const { r, c } = this.player;
            const found = this.crystalPositions.find(
                cp => cp.r === r && cp.c === c && !this.collectedCrystals.includes(cp.id)
            );
            if (found) {
                this.collectedCrystals.push(found.id);
                const crystal = this.crystalTypes.find(ct => ct.id === found.id);
                this.showFlash(`✨ ${crystal.name} collecté !`, 'success');
            }
        },

        checkExit() {
            const { r, c } = this.player;
            if (r === this.exitPos.r && c === this.exitPos.c) {
                if (this.collectedCrystals.length === 7) {
                    this.stopAll();
                    this.initSymbolPuzzle();
                    this.gameState = 'symbol-puzzle';
                } else {
                    const remaining = 7 - this.collectedCrystals.length;
                    this.showFlash(`Il manque encore ${remaining} cristal${remaining > 1 ? 'aux' : ''} !`, 'warning');
                }
            }
        },

        checkMinotaurCatch() {
            if (this.player.r === this.minotaur.r && this.player.c === this.minotaur.c) {
                this.handleCatch();
            }
        },

        // ==============================
        // MINOTAUR (BFS pathfinding)
        // ==============================
        moveMinotaur() {
            if (this.gameState !== 'maze' || !this.maze.length) return;
            const path = this.bfsPath(this.minotaur, this.player);
            if (path.length > 1) {
                this.minotaur = { r: path[1].r, c: path[1].c };
            }
            if (this.minotaur.r === this.player.r && this.minotaur.c === this.player.c) {
                this.handleCatch();
            }
            const dist = Math.abs(this.minotaur.r - this.player.r) + Math.abs(this.minotaur.c - this.player.c);
            this.minotaurWarning = dist <= 3;
        },

        handleCatch() {
            this.minotaurCatches++;
            if (this.minotaurCatches >= 3) {
                this.stopAll();
                this.gameState = 'defeat';
                return;
            }
            this.timeLeft = Math.max(0, this.timeLeft - 120);
            this.minotaur = { r: this.ROWS - 1, c: 0 };
            this.player  = { r: 0, c: 0 };
            this.showFlash(`💀 Capturé par le Minotaure ! −2 minutes. (${this.minotaurCatches}/3)`, 'danger');
        },

        bfsPath(from, to) {
            const queue = [[{ r: from.r, c: from.c }]];
            const visited = new Set([`${from.r},${from.c}`]);
            while (queue.length > 0) {
                const path = queue.shift();
                const curr = path[path.length - 1];
                if (curr.r === to.r && curr.c === to.c) return path;
                for (const nb of this.getPassageNeighbors(curr)) {
                    const key = `${nb.r},${nb.c}`;
                    if (!visited.has(key)) {
                        visited.add(key);
                        queue.push([...path, nb]);
                    }
                }
            }
            return [from];
        },

        getPassageNeighbors({ r, c }) {
            const cell = this.maze[r][c];
            const nb = [];
            if (!cell.N && r > 0)             nb.push({ r: r-1, c });
            if (!cell.S && r < this.ROWS - 1) nb.push({ r: r+1, c });
            if (!cell.W && c > 0)             nb.push({ r, c: c-1 });
            if (!cell.E && c < this.COLS - 1) nb.push({ r, c: c+1 });
            return nb;
        },

        // ==============================
        // TIMERS
        // ==============================
        startTimers() {
            this.timerInterval = setInterval(() => {
                if (this.gameState !== 'maze') return;
                this.timeLeft--;
                if (this.timeLeft <= 0) {
                    this.stopAll();
                    this.gameState = 'defeat';
                }
            }, 1000);

            this.minotaurInterval = setInterval(() => {
                this.moveMinotaur();
            }, 900);
        },

        // ==============================
        // CANVAS RENDERING
        // ==============================
        setupCanvas() {
            const canvas = this.$refs.mazeCanvas;
            if (!canvas) return;
            const wrapper = canvas.parentElement;
            if (wrapper) {
                const maxW = Math.min(wrapper.clientWidth - 4, 510);
                this.CELL = Math.max(20, Math.floor(maxW / this.COLS));
            }
            canvas.width  = this.COLS * this.CELL;
            canvas.height = this.ROWS * this.CELL;
        },

        startRender() {
            const loop = () => {
                if (this.gameState === 'maze') {
                    this.drawMaze();
                    this.animFrame = requestAnimationFrame(loop);
                }
            };
            this.animFrame = requestAnimationFrame(loop);
        },

        drawMaze() {
            const canvas = this.$refs.mazeCanvas;
            if (!canvas || !this.maze.length) return;
            const ctx = canvas.getContext('2d');
            const C = this.CELL;
            const W = this.COLS * C;
            const H = this.ROWS * C;
            const now = Date.now();

            // Background
            ctx.fillStyle = '#07071a';
            ctx.fillRect(0, 0, W, H);

            // Cell floors
            for (let r = 0; r < this.ROWS; r++) {
                for (let c = 0; c < this.COLS; c++) {
                    ctx.fillStyle = 'rgba(255,255,255,0.018)';
                    ctx.fillRect(c * C + 1, r * C + 1, C - 2, C - 2);
                }
            }

            // Exit marker
            {
                const pulse = 0.6 + 0.4 * Math.sin(now / 600);
                ctx.fillStyle = `rgba(255, 215, 0, ${pulse * 0.35})`;
                ctx.fillRect(this.exitPos.c * C + 1, this.exitPos.r * C + 1, C - 2, C - 2);
                ctx.fillStyle = '#ffd700';
                ctx.font = `bold ${Math.round(C * 0.5)}px Georgia`;
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText('G', this.exitPos.c * C + C / 2, this.exitPos.r * C + C / 2);
            }

            // Crystals
            for (const cp of this.crystalPositions) {
                if (this.collectedCrystals.includes(cp.id)) continue;
                const crystal = this.crystalTypes.find(ct => ct.id === cp.id);
                const x = cp.c * C + C / 2;
                const y = cp.r * C + C / 2;
                const pulse = 0.8 + 0.2 * Math.sin(now / 450 + cp.r + cp.c);
                const r = Math.max(3, C / 2 - 4) * pulse;
                ctx.beginPath();
                ctx.arc(x, y, r, 0, Math.PI * 2);
                ctx.fillStyle = crystal.color + 'cc';
                ctx.fill();
                ctx.beginPath();
                ctx.arc(x, y, r + 3, 0, Math.PI * 2);
                ctx.strokeStyle = crystal.color + '66';
                ctx.lineWidth = 1.5;
                ctx.stroke();
            }

            // Minotaur
            {
                const x = this.minotaur.c * C + C / 2;
                const y = this.minotaur.r * C + C / 2;
                ctx.beginPath();
                ctx.arc(x, y, C / 2 - 2, 0, Math.PI * 2);
                ctx.fillStyle = '#8b0000';
                ctx.fill();
                const fs = Math.max(10, C - 8);
                ctx.font = `${fs}px serif`;
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText('🐂', x, y);
            }

            // Player (drawn after minotaur so it renders on top)
            {
                const x = this.player.c * C + C / 2;
                const y = this.player.r * C + C / 2;
                ctx.beginPath();
                ctx.arc(x, y, C / 2 - 2, 0, Math.PI * 2);
                ctx.fillStyle = '#3a1a60';
                ctx.fill();
                ctx.beginPath();
                ctx.arc(x, y, C / 2 - 2, 0, Math.PI * 2);
                ctx.strokeStyle = '#c9a8ff';
                ctx.lineWidth = 2;
                ctx.stroke();
                const fs = Math.max(10, C - 8);
                ctx.font = `${fs}px serif`;
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText('🧙', x, y);
            }

            // Walls (drawn last so they appear over sprites)
            ctx.strokeStyle = '#6a35a8';
            ctx.lineWidth = 2;
            for (let r = 0; r < this.ROWS; r++) {
                for (let c = 0; c < this.COLS; c++) {
                    const cell = this.maze[r][c];
                    const x = c * C;
                    const y = r * C;
                    if (cell.N) { ctx.beginPath(); ctx.moveTo(x, y);     ctx.lineTo(x + C, y);     ctx.stroke(); }
                    if (cell.S) { ctx.beginPath(); ctx.moveTo(x, y + C); ctx.lineTo(x + C, y + C); ctx.stroke(); }
                    if (cell.W) { ctx.beginPath(); ctx.moveTo(x, y);     ctx.lineTo(x, y + C);     ctx.stroke(); }
                    if (cell.E) { ctx.beginPath(); ctx.moveTo(x + C, y); ctx.lineTo(x + C, y + C); ctx.stroke(); }
                }
            }

            // Fog of war
            this.drawFog(ctx, W, H, C);
        },

        drawFog(ctx, W, H, C) {
            const fog = document.createElement('canvas');
            fog.width  = W;
            fog.height = H;
            const fc = fog.getContext('2d');

            fc.fillStyle = 'rgba(7, 7, 26, 1)';
            fc.fillRect(0, 0, W, H);

            const px = this.player.c * C + C / 2;
            const py = this.player.r * C + C / 2;
            const radius = 4.5 * C;

            const grad = fc.createRadialGradient(px, py, 0, px, py, radius);
            grad.addColorStop(0,    'rgba(0,0,0,1)');
            grad.addColorStop(0.55, 'rgba(0,0,0,1)');
            grad.addColorStop(1,    'rgba(0,0,0,0)');

            fc.globalCompositeOperation = 'destination-out';
            fc.fillStyle = grad;
            fc.beginPath();
            fc.arc(px, py, radius, 0, Math.PI * 2);
            fc.fill();

            ctx.drawImage(fog, 0, 0);
        },

        // ==============================
        // SYMBOL PUZZLE
        // ==============================
        initSymbolPuzzle() {
            const runes = [
                'ᚠ','ᚢ','ᚦ','ᚨ','ᚱ','ᚲ','ᚷ','ᚹ','ᚺ','ᚾ',
                'ᛁ','ᛃ','ᛇ','ᛈ','ᛉ','ᛊ','ᛏ','ᛚ','ᛗ','ᛞ',
            ];
            const positions = Array.from({ length: 20 }, (_, i) => i);
            const shuffled  = [...positions].sort(() => Math.random() - 0.5);
            const colorSlots = new Set(shuffled.slice(0, 4));

            let colorIdx = 0;
            const colorOrder = [...this.symbolOrder];

            this.symbols = positions.map(i => ({
                id: i,
                glyph:    runes[i],
                colorKey: colorSlots.has(i) ? colorOrder[colorIdx++] : null,
                activated: false,
                wrong:     false,
            }));

            this.symbolClicked = [];
            this.symbolError   = '';
        },

        clickSymbol(sym) {
            if (!sym.colorKey || sym.activated || sym.wrong) return;

            const expected = this.symbolOrder[this.symbolClicked.length];
            if (sym.colorKey === expected) {
                sym.activated = true;
                this.symbolClicked.push(sym.colorKey);
                this.symbolError = '';
                if (this.symbolClicked.length === this.symbolOrder.length) {
                    setTimeout(() => { this.gameState = 'victory'; }, 700);
                }
            } else {
                sym.wrong = true;
                this.symbolError = 'Mauvais ordre ! La séquence doit respecter les éléments primordiaux.';
                setTimeout(() => {
                    this.symbols.forEach(s => { s.activated = false; s.wrong = false; });
                    this.symbolClicked = [];
                    this.symbolError   = '';
                }, 1000);
            }
        },

        // ==============================
        // FLASH MESSAGE
        // ==============================
        showFlash(msg, type = 'info') {
            this.flashMessage = msg;
            this.flashType    = type;
            clearTimeout(this.flashTimeout);
            this.flashTimeout = setTimeout(() => { this.flashMessage = ''; }, 2800);
        },
    },

    mounted() {
        this._keyHandler = (e) => this.onKeyDown(e);
        document.addEventListener('keydown', this._keyHandler);
    },

    beforeDestroy() {
        document.removeEventListener('keydown', this._keyHandler);
        this.stopAll();
    },
};
</script>

<style scoped>
/* ===========================
   BASE
=========================== */
.cristal-game {
    min-height: 100vh;
    background: linear-gradient(135deg, #07071a 0%, #150a2e 50%, #0a0a1f 100%);
    color: #e8e0f0;
    font-family: 'Georgia', serif;
    padding-bottom: 60px;
    outline: none;
}

/* ===========================
   HEADER
=========================== */
.cristal-game-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 18px 28px;
    background: rgba(0,0,0,0.45);
    border-bottom: 2px solid #6a35a8;
}
.cristal-game-title-block {
    display: flex;
    align-items: center;
    gap: 10px;
}
.cristal-gem-icon {
    font-size: 28px;
    animation: glow-pulse 2.5s ease-in-out infinite;
}
.cristal-game-header h1 {
    margin: 0;
    font-size: 1.5rem;
    color: #c9a8ff;
    text-shadow: 0 0 18px rgba(138,43,226,0.7);
}
.cristal-timer {
    background: rgba(106,53,168,0.3);
    border: 1px solid #6a35a8;
    border-radius: 8px;
    padding: 7px 16px;
    font-size: 1.1rem;
    color: #c9a8ff;
    font-family: monospace;
}
.timer-danger { color: #ff6b6b; }

/* ===========================
   INTRO
=========================== */
.cristal-scene { max-width: 820px; margin: 36px auto; padding: 0 18px; }
.intro-scene { text-align: center; }
.intro-content { max-width: 620px; margin: 0 auto; padding: 20px; }
.intro-gem-anim {
    font-size: 80px;
    animation: glow-pulse 2.5s ease-in-out infinite;
    display: block;
    margin-bottom: 18px;
}
.intro-content h2 { color: #c9a8ff; font-size: 1.9rem; margin-bottom: 16px; }
.intro-text { font-size: 1rem; line-height: 1.8; color: #ccc; margin-bottom: 12px; }

.crystals-grid {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 10px;
    margin: 22px 0;
}
.crystal-info {
    display: flex;
    align-items: center;
    gap: 8px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(106,53,168,0.4);
    border-radius: 20px;
    padding: 6px 14px;
    font-size: 0.88rem;
    color: #ddd;
}
.crystal-dot {
    width: 12px; height: 12px;
    border-radius: 50%;
    display: inline-block;
    flex-shrink: 0;
}

.intro-rules {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
    margin: 22px auto;
    max-width: 460px;
    text-align: left;
}
.rule { display: flex; align-items: center; gap: 10px; color: #c9a8ff; font-size: 0.92rem; }

/* ===========================
   MAZE SCENE
=========================== */
.maze-scene { padding: 0; }

.maze-hud {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 18px;
    background: rgba(0,0,0,0.4);
    border-bottom: 1px solid #3a1a60;
    flex-wrap: wrap;
    gap: 8px;
}
.hud-crystals { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
.crystal-hud-badge {
    width: 22px; height: 22px;
    border-radius: 50%;
    border: 2px solid rgba(255,255,255,0.15);
    display: flex; align-items: center; justify-content: center;
    transition: border-color 0.3s, transform 0.2s;
    opacity: 0.35;
}
.crystal-hud-badge.collected {
    opacity: 1;
    transform: scale(1.15);
    border-color: rgba(255,255,255,0.5);
}
.crystal-hud-dot {
    width: 12px; height: 12px;
    border-radius: 50%;
    display: block;
}
.hud-count { font-size: 0.85rem; color: #c9a8ff; margin-left: 4px; }
.hud-right { display: flex; align-items: center; gap: 12px; }
.hud-warning { color: #ffd700; font-size: 0.88rem; animation: blink 0.8s step-end infinite; }
.hud-catches { color: #ff6b6b; font-size: 0.88rem; }

.canvas-wrapper {
    display: flex;
    justify-content: center;
    padding: 12px 0 8px;
    overflow: auto;
}
canvas {
    display: block;
    border: 2px solid #6a35a8;
    border-radius: 4px;
    box-shadow: 0 0 30px rgba(106,53,168,0.4);
}

.maze-legend {
    display: flex;
    justify-content: center;
    gap: 18px;
    font-size: 0.78rem;
    color: #888;
    padding: 6px 0;
    flex-wrap: wrap;
}
.legend-dot { font-size: 0.6rem; margin-right: 3px; }

/* Flash */
.flash-message {
    position: fixed;
    top: 80px; left: 50%;
    transform: translateX(-50%);
    z-index: 999;
    padding: 10px 22px;
    border-radius: 10px;
    font-size: 0.95rem;
    font-family: Georgia, serif;
    white-space: nowrap;
    pointer-events: none;
    box-shadow: 0 4px 20px rgba(0,0,0,0.5);
}
.flash-success { background: rgba(92,184,92,0.9); color: #fff; }
.flash-warning { background: rgba(240,173,78,0.9); color: #111; }
.flash-danger  { background: rgba(220,53,69,0.9);  color: #fff; }
.flash-info    { background: rgba(106,53,168,0.9);  color: #fff; }

/* Mobile controls */
.mobile-controls {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    padding: 10px 0 4px;
}
.mobile-row { display: flex; gap: 4px; }
.mobile-btn {
    width: 54px; height: 54px;
    background: rgba(106,53,168,0.4);
    border: 1px solid #6a35a8;
    border-radius: 10px;
    color: #c9a8ff;
    font-size: 1.4rem;
    cursor: pointer;
    transition: background 0.15s;
    touch-action: manipulation;
}
.mobile-btn:active { background: rgba(106,53,168,0.7); }

/* ===========================
   SYMBOL PUZZLE
=========================== */
.scene-header h2 { color: #c9a8ff; font-size: 1.4rem; border-bottom: 2px solid #6a35a8; padding-bottom: 10px; margin-bottom: 20px; }
.scene-description { background: rgba(255,255,255,0.04); border-left: 4px solid #6a35a8; border-radius: 0 8px 8px 0; padding: 18px 22px; margin-bottom: 26px; line-height: 1.75; }
.scene-description p { margin-bottom: 10px; }

.symbol-order-hint {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 4px;
    margin-top: 12px;
    padding: 12px 14px;
    background: rgba(0,0,0,0.25);
    border: 1px solid #4a2580;
    border-radius: 8px;
}
.symbol-hint-step { display: flex; align-items: center; gap: 6px; }
.symbol-hint-dot { width: 18px; height: 18px; border-radius: 50%; display: inline-block; flex-shrink: 0; border: 2px solid rgba(255,255,255,0.3); }
.symbol-hint-name { font-size: 0.85rem; color: #ccc; }
.symbol-hint-arrow { color: #555; font-size: 0.9rem; margin: 0 2px; }

.symbols-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 8px;
    margin-bottom: 20px;
}
.rune-symbol {
    aspect-ratio: 1;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem;
    border: 2px solid rgba(255,255,255,0.08);
    border-radius: 8px;
    cursor: default;
    transition: all 0.2s;
    background: rgba(255,255,255,0.03);
    color: #444;
    user-select: none;
}
.rune-colored {
    cursor: pointer;
    color: inherit;
    border-width: 2px;
    background: rgba(255,255,255,0.05);
}
.rune-colored:hover { transform: scale(1.08); filter: brightness(1.2); }
.rune-activated { transform: scale(1.05); }
.rune-wrong { animation: shake 0.4s ease; opacity: 0.4; }
.rune-plain { opacity: 0.2; }

.error-msg { color: #ff6b6b; margin-bottom: 12px; font-size: 0.9rem; }

.symbol-progress {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.88rem;
    color: #888;
}
.progress-dot {
    width: 14px; height: 14px;
    border-radius: 50%;
    border: 2px solid #555;
    display: inline-block;
    transition: background 0.3s, border-color 0.3s;
}
.progress-dot.filled { border-color: transparent; }

/* ===========================
   VICTORY
=========================== */
.victory-scene { text-align: center; }
.victory-content { max-width: 580px; margin: 0 auto; }
.victory-gem-anim { font-size: 90px; animation: victory-bounce 1s ease-in-out infinite alternate; display: block; margin-bottom: 22px; }
.victory-scene h2 { color: #ffd700; font-size: 1.9rem; text-shadow: 0 0 26px rgba(255,215,0,0.6); margin-bottom: 18px; }
.victory-text { font-size: 1rem; line-height: 1.8; color: #ccc; margin-bottom: 28px; }
.victory-stats { display: flex; justify-content: center; gap: 20px; flex-wrap: wrap; margin-bottom: 28px; }
.stat-block { background: rgba(106,53,168,0.2); border: 1px solid #6a35a8; border-radius: 10px; padding: 14px 22px; display: flex; flex-direction: column; align-items: center; gap: 6px; min-width: 110px; }
.stat-block i { font-size: 1.3rem; color: #c9a8ff; }
.stat-block span { font-size: 0.78rem; color: #888; text-transform: uppercase; letter-spacing: 1px; }
.stat-block strong { color: #ffd700; font-size: 0.98rem; }
.victory-buttons { display: flex; justify-content: center; gap: 14px; flex-wrap: wrap; }

/* ===========================
   DEFEAT
=========================== */
.defeat-scene { text-align: center; }
.defeat-content { max-width: 500px; margin: 0 auto; }
.defeat-icon { font-size: 80px; margin-bottom: 18px; }
.defeat-scene h2 { color: #ff6b6b; font-size: 1.8rem; margin-bottom: 16px; }
.defeat-text { font-size: 1rem; line-height: 1.75; color: #ccc; margin-bottom: 16px; }
.defeat-stats { margin-bottom: 22px; color: #aaa; font-size: 0.95rem; }
.defeat-stats strong { color: #c9a8ff; }

/* ===========================
   BUTTONS
=========================== */
.btn-cristal {
    background: linear-gradient(135deg, #6a35a8, #4a2580);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 11px 26px;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-family: Georgia, serif;
}
.btn-cristal:hover {
    background: linear-gradient(135deg, #8a55c8, #6a35a8);
    transform: translateY(-1px);
    box-shadow: 0 4px 18px rgba(106,53,168,0.5);
    color: #fff;
}
.btn-secondary-cristal { background: rgba(255,255,255,0.08); border: 1px solid #6a35a8; }
.btn-secondary-cristal:hover { background: rgba(255,255,255,0.14); }

/* ===========================
   ANIMATIONS
=========================== */
@keyframes glow-pulse {
    0%, 100% { filter: drop-shadow(0 0 6px rgba(138,43,226,0.6)); }
    50%       { filter: drop-shadow(0 0 22px rgba(138,43,226,1)); }
}
@keyframes victory-bounce {
    0%   { transform: translateY(0) scale(1);    filter: drop-shadow(0 0 10px rgba(255,215,0,0.6)); }
    100% { transform: translateY(-14px) scale(1.06); filter: drop-shadow(0 0 28px rgba(255,215,0,1)); }
}
@keyframes blink {
    0%, 100% { opacity: 1; }
    50%       { opacity: 0.3; }
}
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25%       { transform: translateX(-5px); }
    75%       { transform: translateX(5px); }
}
.fade-enter-active, .fade-leave-active { transition: opacity 0.4s; }
.fade-enter, .fade-leave-to { opacity: 0; }

/* ===========================
   RESPONSIVE
=========================== */
@media (max-width: 600px) {
    .cristal-game-header { flex-direction: column; gap: 8px; text-align: center; }
    .cristal-game-header h1 { font-size: 1.1rem; }
    .symbols-grid { grid-template-columns: repeat(4, 1fr); }
    .symbol-order-hint { flex-direction: column; align-items: flex-start; }
    .victory-stats { flex-direction: column; align-items: center; }
    .mobile-btn { width: 46px; height: 46px; font-size: 1.2rem; }
}
</style>
