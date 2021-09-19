let user = Window.App.user;

module.exports = {
    owns (model, props = 'user_id') {
        return model[props] === user.id;
    },

    isAdmin() {
        return ['JohnDoe', 'hadi'].includes(user.name);
    }
};
