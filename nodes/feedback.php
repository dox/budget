<div class="box">
                <form method="POST" action="/ideas">
                    <input type="hidden" name="_token" value="ZZxLKKzw7Jk0G1KVLSUXm7tNcF9eMLxH9nqH5LxW">
                    <div class="box__section">
                        <div class="input input--small">
                            <label>Type</label>
                            <select name="type">
                                <option value="bug">Bug or Error</option>
                                <option value="feature_request">Feature Request or Suggestion</option>
                            </select>
                        </div>
                        <div class="input input--small">
                            <label>Body</label>
                            <textarea name="body"></textarea>
                        </div>
                        <button class="button">Submit</button>
                    </div>
                </form>
            </div>