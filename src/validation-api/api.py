from flask import Flask, request, json
from flask_cors import CORS
from validator.validator import ValidationOptions, ValidationResult, ValidationRun

app = Flask(__name__)
CORS(app)

@app.route("/validate", methods=["POST"])
def validate():
    validation_json = request.get_json()
    if validation_json is None:
        print("Error")
    else:
        print(validation_json)

    response = json.jsonify({'hey': 'yeah'})
    response.headers.add('Access-Control-Allow-Origin', '*')
    return response

if __name__ == '__main__':
    app.run(debug=True)