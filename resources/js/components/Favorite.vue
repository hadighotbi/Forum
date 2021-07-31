<template>
        <button type="button" :class="classes" @click="toggle" >
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-heart-fill" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z"/>
            </svg>
            <span v-text="count"></span>
        </button>
</template>

<script>
    export default {
        props:['reply'],

        data(){
            return {
                count: this.reply.favoritesCount,
                active: this.reply.isFavorited
            };
        },

        computed: {
            classes() {
                return ['btn', this.active ? 'btn-primary' : 'btn-outline-danger'];
            },
            endpoint() {
                return '/replies/' + this.reply.id + '/favorites';
            }
        },

        methods:{
            toggle() {
                return this.active ? this.destroy() : this.create()
            },
            create() {
                axios.post( this.endpoint );
                this.active = true;
                this.count++;
            },
            destroy() {
                axios.delete( this.endpoint );
                this.active = false;
                this.count--;
            }
        }
    }
</script>
