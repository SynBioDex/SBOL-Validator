from flask import Flask, request, json
from flask_cors import CORS
from util import do_validation


app = Flask(__name__)
CORS(app)

@app.route("/validate", methods=["POST"])
def validate():
    validation_json = request.get_json()
    if validation_json is None:
        raise InvalidUsage("Request content type must be application/json")
    else:
        response = json.jsonify(do_validation(validation_json))
        response.headers.add('Access-Control-Allow-Origin', '*')
        return response

    

class InvalidUsage(Exception):
    status_code = 400

    def __init__(self, message, status_code=None, payload=None):
        Exception.__init__(self)
        self.message = message
        if status_code is not None:
            self.status_code = status_code
        self.payload = payload

    def to_dict(self):
        rv = dict(self.payload or ())
        rv['message'] = self.message
        return rv

@app.errorhandler(InvalidUsage)
def handle_invalid_usage(error):
    response = jsonify(error.to_dict())
    response.status_code = error.status_code
    return response

if __name__ == '__main__':
    app.run(debug=True)