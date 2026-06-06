import sys
import os
import json
import urllib.parse
import requests
from bs4 import BeautifulSoup
import litellm

def load_gemini_api_key():
    if "GEMINI_API_KEY" in os.environ:
        return os.environ["GEMINI_API_KEY"]
    
    script_dir = os.path.dirname(os.path.abspath(__file__))
    possible_dirs = [
        script_dir,
        os.path.dirname(script_dir),  # public_html
        os.path.dirname(os.path.dirname(script_dir)),  # VPS
        os.path.dirname(os.path.dirname(os.path.dirname(script_dir))),  # PROJETO_AME
    ]
    for d in possible_dirs:
        env_path = os.path.join(d, ".env")
        if os.path.isfile(env_path):
            try:
                with open(env_path, "r", encoding="utf-8") as f:
                    for line in f:
                        line = line.strip()
                        if not line or line.startswith("#"):
                            continue
                        if "=" in line:
                            parts = line.split("=", 1)
                            key = parts[0].strip()
                            if key == "GEMINI_API_KEY":
                                val = parts[1].strip()
                                if (val.startswith('"') and val.endswith('"')) or (val.startswith("'") and val.endswith("'")):
                                    val = val[1:-1]
                                return val
            except Exception:
                pass
    return None

def ddg_search(query):
    headers = {
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
    }
    url = f"https://html.duckduckgo.com/html/?q={urllib.parse.quote(query)}"
    try:
        r = requests.get(url, headers=headers, timeout=10)
        if r.status_code != 200:
            return ""
        soup = BeautifulSoup(r.text, 'html.parser')
        results = []
        for a in soup.find_all('a', class_='result__snippet'):
            results.append(a.text.strip())
        return "\n".join(results[:5])
    except Exception as e:
        return f"Error searching: {str(e)}"

def clean_json_response(content):
    content = content.strip()
    if content.startswith("```json"):
        content = content[7:]
    elif content.startswith("```"):
        content = content[3:]
    if content.endswith("```"):
        content = content[:-3]
    return content.strip()

def main():
    if len(sys.argv) < 2:
        print(json.dumps({"success": False, "error": "Missing JSON events argument"}))
        sys.exit(1)
        
    api_key = load_gemini_api_key()
    
    # Configure model and API base based on key presence
    if api_key:
        model_name = "gemini/gemini-1.5-flash"
        api_base = None
    else:
        # Fallback to LiteLLM proxy as instructed by parent
        api_key = "sk-Pjbj4sgDUf6lH-exN2jPdQ"
        model_name = "openai/text-chat"
        api_base = "https://llm.netmailing.com.br"
        
    if not api_key:
        print(json.dumps({"success": False, "error": "GEMINI_API_KEY not found"}))
        sys.exit(1)
        
    # Configure litellm global variables
    if api_base:
        litellm.api_base = api_base
    litellm.api_key = api_key
    
    try:
        events = json.loads(sys.argv[1])
    except Exception:
        # Fallback to base64 decoding (useful to bypass Windows cmd line escaping issues)
        try:
            import base64
            raw_arg = sys.argv[1]
            decoded = base64.b64decode(raw_arg).decode('utf-8')
            events = json.loads(decoded)
        except Exception as e:
            print(json.dumps({"success": False, "error": f"Invalid JSON/Base64 input: {str(e)}"}))
            sys.exit(1)
            
    results = []
    for event in events:
        evento_id = event.get("evento_id")
        evento_nome = event.get("Evento")
        
        if not evento_nome or not evento_id:
            continue
            
        # Formulate query
        query = f"Feira {evento_nome} próxima edição data 2026 2027"
        snippets = ddg_search(query)
        
        prompt = f"""
Você é um extrator de datas especializado em feiras e eventos.
O evento analisado é: "{evento_nome}"
Estamos no ano 2026.

Abaixo estão os snippets resultantes de uma busca na web sobre a próxima edição deste evento:
---
{snippets}
---

Instruções:
1. Extraia a data de início e a data de fim da PRÓXIMA edição do evento (pode ser em 2026, 2027 ou posterior).
2. Retorne o resultado estritamente no formato JSON abaixo, sem qualquer outro texto ou markdown:
{{"inicio": "YYYY-MM-DD", "final": "YYYY-MM-DD"}}

Regras:
- Se as datas forem encontradas, use o formato YYYY-MM-DD.
- Se o ano não estiver explícito mas ficar claro que se refere à edição atual/próxima de 2026/2027, assuma o ano correspondente.
- Se a data de início ou final não for encontrada de forma alguma nos snippets, use null para o respectivo campo.
- Retorne APENAS o JSON. Sem comentários, sem explicações.
"""
        
        try:
            response = litellm.completion(
                model=model_name,
                messages=[{"role": "user", "content": prompt}],
                api_key=api_key
            )
            content = response.choices[0].message.content
            cleaned = clean_json_response(content)
            parsed = json.loads(cleaned)
            
            results.append({
                "evento_id": evento_id,
                "inicio": parsed.get("inicio"),
                "final": parsed.get("final")
            })
        except Exception as e:
            results.append({
                "evento_id": evento_id,
                "inicio": None,
                "final": None,
                "error": str(e)
            })
            
    print(json.dumps(results, ensure_ascii=False))

if __name__ == "__main__":
    main()
