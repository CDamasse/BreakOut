<template>
    <transition :name="transition">
        <div v-show="visible">
            <slot></slot>
            <!-- slot appelle le contenu indiqué par le parent lorsque celui-ci appelle le composant CarouselSlide -->
            <!--Index : {{ index }}-->
        </div>
    </transition>
</template>

<script>
export default {
    data () {
        return {
            index: 0
        }
    },
    computed: {
        transition () {
            if (this.$parent.direction) {
                return 'slide-' + this.$parent.direction
            }
        },
        visible () {
            return this.index === this.$parent.index
        }
    }

}
</script>
<style>
.slide-right-enter-active{
    animation: slideRightIn 0.5s;
}

.slide-right-leave-active {
    animation: slideRightOut 0.5s;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: 100%;
}

@keyframes slideRightIn {
    from { transform: translateX(100%); }
    to { transform: translateX(0); }
}

@keyframes slideRightOut {
    from { transform: translateX(0); }
    to { transform: translateX(-100%); }
}

.slide-left-enter-active{
    animation: slideLeftIn 0.5s;
}

.slide-left-leave-active {
    animation: slideLeftOut 0.5s;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: 100%;
}

@keyframes slideLeftIn {
    from { transform: translateX(-100%); }
    to { transform: translateX(0); }
}

@keyframes slideLeftOut {
    from { transform: translateX(0); }
    to { transform: translateX(100%); }
}
</style>
