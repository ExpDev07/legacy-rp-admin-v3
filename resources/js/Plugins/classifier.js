const savedClassifier = require('../data/classifier.json');

import BayesClassifier from "bayes-classifier";

const Classifier = {
	async install(Vue, options) {
		let classifier = false;

		function ensureClassifier() {
			if (!classifier) {
				classifier = new BayesClassifier();
				classifier.restore(savedClassifier);
			}
		}

		Vue.prototype.classifyCharacter = function (character) {
			ensureClassifier();

			const text = character.name + "\n" + character.backstory;

			return classifier.classify(text);
		};
	},
}

export default Classifier;
