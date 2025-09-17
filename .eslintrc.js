module.exports = {
     // Disable some dumb Moodle core rules from https://github.com/moodle/moodle/blob/main/.eslintrc .
    rules: {
        'no-redeclare': 'off', // 'M' is already defined as a built-in global variable.
        '@babel/no-unused-expressions': 'off', // Expected an assignment or function call and instead saw an expression.
        '@babel/new-cap': 'off', // A constructor name should not start with a lowercase letter.
        'no-console': 'off',
        'max-len': 'off',
        // I would like to have this rule on, but have one variable that I want mixed case.
        'camelcase': 'off'
    }
};