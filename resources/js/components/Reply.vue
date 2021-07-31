<template>
    <div :id="'reply-'+id" class="panel panel-default">
        <div class="card" style="margin-bottom: 15px;">
            <div class="card-header">
                <div class="level">
                <span class="flex">
                    <a :href="'/profiles/' + data.owner.name"
                       v-text="data.owner.name">
                    </a> Said <span v-text="ago"></span>
                </span>

                    <div v-if="signedIn">
                        <favorite :reply="data"></favorite>
                    </div>

                </div>
            </div>

            <div class="card-body">
                <div v-if="editing">
                    <div class="form-group">
                        <textarea class="form-control" v-model="body"></textarea>
                    </div>

                    <button class="btn btn-primary btn-sm" @click="update">Update</button>
                    <button type="submit" class="btn btn-link btn-sm" @click="editing = false">Cancel</button>
                </div>
                <div v-else
                     v-text="body"></div>
            </div>

            <div class="panel-footer bg-light level" v-if="canUpdate">
                <button class="btn btn-info btn-sm" style="margin:10px;" @click="editing = true">Edit</button>
                <button class="btn btn-danger btn-sm" style="margin:10px;" @click="destroy">Delete</button>
            </div>
        </div>
    </div>
</template>

<script>
import Favorite from "./Favorite.vue";
import moment from 'moment';

export default {
    props: ['data'],

    components: { Favorite },

    data() {
        return {
            editing: false,
            id: this.data.id,
            body: this.data.body
        };
    },

    computed: {
        ago() {
            return moment(this.data.created_at).fromNow();
        },

        signedIn() {
            return Window.App.signedIn;
        },

        canUpdate() {
            return this.authorize(user => this.data.user_id == user.id);
        }
    },

    methods: {

        update() {
            axios.patch('/replies/' + this.data.id, {
                body: this.body
            });
            this.editing = false;

            flash('Updated!');
        },

        destroy() {
            axios.delete('/replies/' + this.data.id);

            this.$emit('deleted', this.data.id);

            flash('Your reply has been deleted.');
            // $(this.$el).fadeOut(300, () => {
            // });
        }
    }
}
</script>
