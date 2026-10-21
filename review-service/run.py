import os
from app import create_app

if __name__ == '__main__':
    app = create_app()
    app.run(host=os.getenv('HOST', '127.0.0.1'), port=int(os.getenv('PORT', '3002')), debug=app.debug)
