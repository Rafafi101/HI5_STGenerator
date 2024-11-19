from flask import Flask, request, jsonify
from transformers import AutoTokenizer
from groq import Groq
from flask_cors import CORS

app = Flask(__name__)
CORS(app)

client = Groq(
    api_key="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",
)

@app.route('/generate_syllabus', methods=['POST'])
def generate_syllabus():
    skill_name = request.json.get('skill_name')
    proficiency = request.json.get('proficiency')

    if proficiency == "1":
        proficiency = "1 - Basic"
    elif proficiency == "2":
        proficiency == "2 - Intermediate"
    elif proficiency == "3":
        proficiency == "3 - Advanced"
    elif proficiency == "4":
        proficiency == "4 - Expert"

    print(proficiency)
    completion = client.chat.completions.create(
        model="llama3-8b-8192",
        messages=[
            {
            "role": "user",
            "content": "Create a syllabus for python - \"3 - advanced\" for employees. In addition provide the following and use that format:\\n\\nTraining Name:\\nDescription: \\nSyllabus:"
            },
            {
            "role": "assistant",
            "content": "Here is a syllabus for a Python \"3 - Advanced\" training for employees:\n\n**Training Name:** Python 3 - Advanced\n\n**Description:** This advanced Python training is designed for employees who have a solid foundation in Python programming and want to take their skills to the next level. In this course, we will delve into more complex topics such as decorators, generators, and asynchronous programming. We will also cover best practices for writing robust and efficient code.\n\n**Syllabus:**\n\n**Module 1: Decorators and Higher-Order Functions**\n\n* Introduction to decorators: what are they and why are they useful?\n* Creating and using decorators\n* Decorating functions and classes\n* Best practices for using decorators\n\n* Higher-order functions: what are they?\n* Creating and using higher-order functions\n* Using map, filter, and reduce with higher-order functions\n\n**Module 2: Generators and Iterators**\n\n* Introduction to generators: what are they and how do they work?\n* Creating and using generators\n* Working with generator expressions\n* Using generators with for loops and list comprehensions\n\n* Introduction to iterators: what are they and how do they work?\n* Creating and using iterators\n* Using iterators with for loops and list comprehensions\n\n**Module 3: Asynchronous Programming**\n\n* Introduction to asynchronous programming: what is it and why is it useful?\n* Creating and using coroutines\n* Using async/await with functions and decorators\n* Best practices for writing asynchronous code\n\n**Module 4: Best Practices and Advanced Topics**\n\n* Coding style and conventions\n* Debugging and troubleshooting techniques\n* Advanced topics: such as lambda functions, default arguments, and variable scoping\n\n**Module 5: Project and assessment**\n\n* Students will work on a project that incorporates the concepts learned in the course\n* Project will be reviewed and assessed for understanding and application of concepts\n\n**Required Prerequisites:**\n\n* Python 2 - Fundamentals or equivalent experience\n* Python 2 - Intermediate or equivalent experience\n\n**Recommended Textbook:**\n\n* \"Python Crash Course\" by Eric Matthes (Chapters 11-15)\n\n**Course Format:**\n\n* 5 modules, one week apart\n* 2 hours per session, with 30 minutes for questions and answers\n* Project review and assessment will be conducted during the final session\n\n**Evaluation:**\n\n* Participants will be evaluated based on their participation in class discussions and activities\n* Project will be assessed for understanding and application of concepts\n\n**Certification:**\n\n* Upon completion of the course and successful project assessment, participants will receive a certificate of completion.\n\nPlease note that the specifics, such as duration, format, and evaluation, can be adjusted according to your organization's needs and preferences."
            },
            {
                "role": "user",
                "content": f"using the same format, how about the skill: {skill_name} - \"{proficiency}\""
            }
        ],
        temperature=1,
        max_tokens=1024,
        top_p=1,
        stream=True,
        stop=None,
    )

    generated_text = ""
    for chunk in completion:
      generated_text += chunk.choices[0].delta.content or ""

    # Extracting Training Name, Description, and Syllabus
    def extract_section(text, section_name):
      start = text.find(section_name) + len(section_name)
      end = text.find('\n\n', start)
      return text[start:end].strip()

    training_name = extract_section(generated_text, "Training Name:")
    description = extract_section(generated_text, "Description:")

    # Handle Syllabus with targeted formatting removal
    syllabus_start = generated_text.find("Syllabus:") + len("Syllabus:")
    syllabus_lines = generated_text[syllabus_start:].strip().splitlines()  # Split into lines
    cleaned_syllabus = []

    # Clean first two lines (remove leading "**" and newline)
    for i in range(2):
      cleaned_line = syllabus_lines[i].strip().lstrip("* ")
      cleaned_syllabus.append(cleaned_line)

    # Add remaining lines as-is
    cleaned_syllabus.extend(syllabus_lines[2:])

    syllabus = "\n".join(cleaned_syllabus)  # Join cleaned lines with newline

    syllabus = syllabus.lstrip("\n")  # Strip any leading newlines
    syllabus = syllabus.lstrip("\n")  # Strip another leading newline (removes 2)

    training_name = remove_asterisks(training_name)
    description = remove_asterisks(description)
    print(training_name)
    print(description)
    print(syllabus)

    return jsonify({
        "training_name": training_name,
        "description": description,
        "syllabus": syllabus
    })


def extract_skills(gathered_skills):
    # Ensure all items are strings and filter out any None values
    if gathered_skills:
        cleaned_skills = [str(skill) for skill in gathered_skills if skill is not None]
        return ", ".join(cleaned_skills) if cleaned_skills else "None"
    return "None"


def create_prompt(gathered_skills, category):
    # Modify the prompt to handle the "None" case
    gathered_skills_str = f"gathered skills: {gathered_skills}*" if gathered_skills != "None" else "There are no gathered skills."
    return f"{gathered_skills_str}\n\nWhat skills can be associated with the skill category \"{category}\" aside from the skills found in the gathered list? Put them in this list and use the format above where the list is separated by comma and end with an asterisk:\n\nskills = "

def process_response(response):
    match = re.search(r"skills = (.*?)\*", response)
    if match:
        skills_string = match.group(1)
        skills_array = skills_string.strip().split(", ")
        return skills_array
    else:
        return []

def remove_asterisk(text):
    """Removes asterisks from the given text."""
    return text.replace("**", "")

def remove_asterisks(text):
    """Removes asterisks from the given text."""
    return text.replace("** ", "")

@app.route('/waiz_chat', methods=['POST'])
def waiz_chat():
    data = request.json
    prompt = data.get('prompt')
    conversation = data.get('conversation')

    # Append the user's prompt to the conversation
    conversation.append({
        "role": "user",
        "content": prompt
    })

    for message in conversation:
        print(f"Role: {message['role']}, Content: {message['content']}")

    # Generate response using the LLM
    completion = client.chat.completions.create(
        model="llama3-8b-8192",
        messages=conversation,
        temperature=1,
        max_tokens=1024,
        top_p=1,
        stream=True,
        stop=None,
    )

    # Collect the generated response
    generated_text = ""
    for chunk in completion:
        generated_text += chunk.choices[0].delta.content or ""

    print(generated_text)

    # Append the assistant's response to the conversation
    conversation.append({
        "role": "assistant",
        "content": generated_text
    })

    # Process the response to remove asterisks and extract the task
    task = remove_asterisk(generated_text)

    # Return the updated conversation and task as JSON
    return jsonify({
        "task": task,
        "conversation": conversation
    })


if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)
