import sys
import os
import json
import urllib.parse
import litellm
from duckduckgo_search import DDGS

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
    try:
        with DDGS() as ddgs:
            results = list(ddgs.text(query, max_results=10))
            snippets = []
            for r in results:
                title = r.get('title', '')
                body = r.get('body', '')
                href = r.get('href', '')
                snippets.append(f"Title: {title}\nLink: {href}\nSnippet: {body}\n---")
            return "\n".join(snippets)
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
        print(json.dumps({"success": False, "error": "Missing event name argument"}))
        sys.exit(1)
        
    evento_nome = sys.argv[1]
    
    api_key = load_gemini_api_key()
    
    # Configure model and API base based on key presence
    if api_key:
        model_name = "gemini/gemini-1.5-flash"
        api_base = None
    else:
        # Fallback to LiteLLM proxy
        api_key = "sk-q9jlSjI9QLZavKEmQbUHrw"
        model_name = "openai/text-chat"
        api_base = "https://llm.netmailing.com.br"
        
    if api_base:
        litellm.api_base = api_base
    litellm.api_key = api_key
    
    # Query for exhibitors
    queries = [
        f'expositores feira "{evento_nome}" list',
        f'lista de expositores "{evento_nome}" 2026 2027',
        f'exhibitors "{evento_nome}" list'
    ]
    
    search_results = []
    for q in queries[:2]: # Search top 2 queries to gather enough text
        res = ddg_search(q)
        if "Error searching" not in res:
            search_results.append(res)
            
    all_snippets = "\n\n".join(search_results)
    
    prompt = f"""
Você é um extrator de inteligência de negócios. Analise o evento de negócios/feira de exposições: "{evento_nome}".
Abaixo estão os resultados de pesquisa na web sobre os expositores deste evento:
---
{all_snippets}
---

Instruções de Extração:
1. Extraia o máximo de empresas expositoras que foram identificadas como participantes ou listadas como expositores desse evento específico.
2. Para cada empresa/expositor encontrado, tente capturar as seguintes informações se estiverem explícitas ou implícitas nas URLs e snippets:
   - "nome": Nome da empresa (obrigatório).
   - "contato": Nome da pessoa de contato, se disponível (caso contrário null).
   - "telefone": Número de telefone, se disponível (caso contrário null).
   - "email": E-mail da empresa ou contato, se disponível (caso contrário null).
   - "whatsapp": WhatsApp da empresa, se disponível (caso contrário null).
   - "site": Site da empresa (geralmente extraído do link/URL do snippet ou explicitado, caso contrário null).
   - "instagram": Perfil do Instagram, se disponível (caso contrário null).
3. O resultado deve ser estritamente um array JSON válido de objetos com os campos mencionados.
4. NÃO invente informações. Se não houver dados de contato (e-mail, whatsapp, etc.), retorne-os como null.
5. Retorne APENAS o JSON no formato abaixo, sem qualquer outro texto ou markdown:
[
  {{"nome": "Empresa Exemplo", "contato": null, "telefone": null, "email": null, "whatsapp": null, "site": "https://...", "instagram": null}},
  ...
]
"""

    try:
        response = litellm.completion(
            model=model_name,
            messages=[{"role": "user", "content": prompt}],
            api_key=api_key,
            temperature=0.2
        )
        content = response.choices[0].message.content
        cleaned = clean_json_response(content)
        parsed = json.loads(cleaned)
        
        # Ensure it is a list
        if not isinstance(parsed, list):
            parsed = [parsed]
            
        print(json.dumps(parsed, ensure_ascii=False))
    except Exception as e:
        print(json.dumps({"success": False, "error": str(e)}))

if __name__ == "__main__":
    main()
