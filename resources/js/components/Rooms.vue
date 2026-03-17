<template>
    <div>
        <div class="breakout-body">
            <div class="container mt-4 pb-5">
                <div class="panneau-ligne-1 pb-3">
                    <hr>
                    <div class="d-flex justify-content-center">
                        <div><h2>Toutes les salles</h2></div>
                    </div>
                </div>

                <div class="row pb-5">
                    <!-- Salle 1 -->
                    <div class="col-md-4 col-12 mt-3 text-center">
                        <div class="carre-salle-accueil" style="background: url('img/illustrations_salles/exemple1.jpg')">
                            <div class="carre-salle-accueil-apres">
                                <div class="d-flex flex-column w-100">
                                    <div class="w-100 d-flex" style="height: calc(100% - 60px);">
                                        <div class="m-auto">
                                            <h2>Le tombeau<br> d'agamemnon</h2>
                                        </div>
                                    </div>
                                    <div class="w-100" style="height: 60px;">
                                        <div class="h-100">
                                            <button>Bientôt</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="carre-salle-accueil-footer p-2">
                            <div class="d-flex justify-content-between">
                                <div><i class="fas fa-user">&nbsp;</i><i class="fas fa-user"></i>&nbsp;<i class="fas fa-user"></i></div>
                                <div><i class="fas fa-hourglass-half"></i><small> ≃ 30 min</small></div>
                            </div>
                        </div>
                    </div>

                    <!-- Salle 2 -->
                    <div class="col-md-4 col-12 mt-3 text-center">
                        <div class="carre-salle-accueil" style="background: url('img/illustrations_salles/exemple2.png')">
                            <div class="carre-salle-accueil-apres">
                                <div class="d-flex flex-column w-100">
                                    <div class="w-100 d-flex" style="height: calc(100% - 60px);">
                                        <div class="m-auto">
                                            <h2>La forêt<br> d'esrael</h2>
                                        </div>
                                    </div>
                                    <div class="w-100" style="height: 60px;">
                                        <div class="h-100">
                                            <button>Bientôt</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="carre-salle-accueil-footer p-2">
                            <div class="d-flex justify-content-between">
                                <div><i class="fas fa-user">&nbsp;</i><i class="fas fa-user"></i>&nbsp;<i class="fas fa-user"></i></div>
                                <div><i class="fas fa-hourglass-half"></i><small> ≃ 30 min</small></div>
                            </div>
                        </div>
                    </div>

                    <!-- Salle 3 : La Quête du Cristal d'Infini -->
                    <div class="col-md-4 col-12 mt-3 text-center">
                        <div class="carre-salle-accueil cristal-room-card" style="background: linear-gradient(135deg, #0a0a1a 0%, #1a0a2e 100%);">
                            <div class="carre-salle-accueil-apres cristal-room-apres">
                                <div class="d-flex flex-column w-100">
                                    <div class="w-100 d-flex" style="height: calc(100% - 60px);">
                                        <div class="m-auto">
                                            <div class="cristal-card-gem">💎</div>
                                            <h2>La quête du<br> Cristal d'Infini</h2>
                                            <div class="cristal-badge">Disponible</div>
                                        </div>
                                    </div>
                                    <div class="w-100" style="height: 60px;">
                                        <div class="h-100">
                                            <router-link to="/play/cristal-infini">
                                                <button class="btn-cristal-play">Jouer</button>
                                            </router-link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="carre-salle-accueil-footer p-2">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <i class="fas fa-user">&nbsp;</i>
                                    <small class="text-muted">Solo</small>
                                </div>
                                <div><i class="fas fa-hourglass-half"></i><small> ≃ 20 min</small></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <global-footer></global-footer>
        </div>
    </div>
</template>

<script>
import GlobalFooter from "./layout/Footer";
export default {
    name: "Rooms",
    components: {GlobalFooter}
}
</script>

<style scoped>
.cristal-room-card {
    position: relative;
    overflow: hidden;
}

.cristal-room-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 50% 40%, rgba(138, 43, 226, 0.3) 0%, transparent 70%);
    pointer-events: none;
}

.cristal-room-apres {
    background: rgba(10, 10, 26, 0.7) !important;
}

.cristal-card-gem {
    font-size: 40px;
    margin-bottom: 8px;
    animation: gem-float 3s ease-in-out infinite;
}

.cristal-badge {
    display: inline-block;
    background: linear-gradient(135deg, #6a35a8, #4a2580);
    color: #fff;
    font-size: 0.7rem;
    padding: 2px 10px;
    border-radius: 12px;
    margin-top: 6px;
    letter-spacing: 1px;
    text-transform: uppercase;
}

.btn-cristal-play {
    background: linear-gradient(135deg, #6a35a8, #4a2580);
    color: #fff;
    border: none;
    padding: 8px 24px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.9rem;
    transition: all 0.2s;
}

.btn-cristal-play:hover {
    background: linear-gradient(135deg, #8a55c8, #6a35a8);
    box-shadow: 0 4px 16px rgba(106, 53, 168, 0.5);
}

@keyframes gem-float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
}
</style>
