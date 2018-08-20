/**
 * 用户自定义脚本.
 */
(function(window, Object, undefined) {

// define a user behaviour
var Main = qc.defineBehaviour('qc.engine.Main', qc.Behaviour, function() {
    // need this behaviour be scheduled in editor
    //this.runInEditor = true;
    this.success_btn = null;
    this.lose_btn = null;
    this.gain_btn = null;
    this.graphics = null;
    this.need_redraw = true;
}, {
    // fields need to be serialized
    success_btn: qc.Serializer.NODE,
    lose_btn: qc.Serializer.NODE,
    gain_btn: qc.Serializer.NODE,
    graphics: qc.Serializer.NODE,
});

// Called when the script instance is being loaded.
Main.prototype.awake = function() {
    var self = this;
    this.game.world.onSizeChange.add(function() {
        self.need_redraw = true;
    });

    self.success_btn.value = 50;
    self.lose_btn.value = 1;
    self.gain_btn.value = 1;
    self.success_btn.initY = self.success_btn.anchoredY;
    self.lose_btn.initX = self.lose_btn.anchoredX;
    self.gain_btn.initX = self.gain_btn.anchoredX;

    var _onDrag = function(node, pointerEvent) {
        var startX = pointerEvent.source.startX, startY = pointerEvent.source.startY;
        var x = pointerEvent.source.x, y = pointerEvent.source.y;
        self.need_redraw = true;
        self.fixValue();

        // gain * success = lose * (100 - success);
        if (node.name == "success") {
            // 4 逻辑坐标对应数值 1
            var deltaY = pointerEvent.source.deltaY;
            var success = node.value - deltaY / 4;
            success = self.game.math.min(99.99, success);
            success = self.game.math.max(0.01, success);
            self.success_btn.value = success;
            if (success >= 50) {
                self.gain_btn.value = 1;
                self.lose_btn.value = success / (100 - success);
            }
            else {
                self.lose_btn.value = 1;
                self.gain_btn.value = (100 - success) / success;
            }
            self.fixPosition();
        }
        else if (node.name == "lose") {
            // 20 逻辑坐标对应数值 1
            var deltaX = pointerEvent.source.deltaX;
            var lose = node.value - deltaX / 20;
            lose = self.game.math.max(1, lose);
            self.lose_btn.value = lose;
            self.gain_btn.value = 1;
            self.success_btn.value = 100 * self.lose_btn.value / (self.lose_btn.value + self.gain_btn.value);
            self.fixPosition();
        }
        else if (node.name == "gain") {
            // 20 逻辑坐标对应数值 1
            var deltaX = pointerEvent.source.deltaX;
            var gain = node.value + deltaX / 20;
            gain = self.game.math.max(1, gain);
            self.gain_btn.value = gain;
            self.lose_btn.value = 1;
            self.success_btn.value = 100 * self.lose_btn.value / (self.lose_btn.value + self.gain_btn.value);
            self.fixPosition();
        }
    }
    self.success_btn.addListener(self.success_btn.onDrag, _onDrag, self.success_btn);
    self.lose_btn.addListener(self.lose_btn.onDrag, _onDrag, self.lose_btn);
    self.gain_btn.addListener(self.gain_btn.onDrag, _onDrag, self.gain_btn);
};

Main.prototype.fixPosition = function() {
    this.success_btn.anchoredY = this.success_btn.initY - (this.success_btn.value - 50) * 4;
    this.lose_btn.anchoredX = (1 - this.lose_btn.value) * 20 + this.lose_btn.initX;
    this.gain_btn.anchoredX = (this.gain_btn.value - 1) * 20 + this.gain_btn.initX;

    this.success_btn.find("value").text = this.success_btn.value.toFixed(2) + "%";
    this.lose_btn.find("value").text = this.lose_btn.value.toFixed(2);
    this.gain_btn.find("value").text = this.gain_btn.value.toFixed(2);
}

Main.prototype.fixValue = function() {
    if (this.lose_btn.value < 1 && this.gain_btn.value < 1)  {
        this.gain_btn.value = 1;
        this.lose_btn.value = 1;
    }
    else if (this.lose_btn.value < 1) {
        this.gain_btn.value = this.gain_btn.value / this.lose_btn.value;
        this.lose_btn.value = 1;
    }
    else if (this.gain_btn.value < 1) {
        this.lose_btn.value = this.lose_btn.value / this.gain_btn.value;
        this.gain_btn.value = 1;
    }
    else if (this.lose_btn.value > 1 && this.gain_btn.value > 1) {
        if (this.lose_btn.value >= this.gain_btn.value) {
            this.lose_btn.value = this.lose_btn.value / this.gain_btn.value;
            this.gain_btn.value = 1;
        }
        else {
            this.gain_btn.value = this.gain_btn.value / this.lose_btn.value;
            this.lose_btn.value = 1;
        }
    }
    this.success_btn.value = 100 * this.lose_btn.value / (this.lose_btn.value + this.gain_btn.value);
    this.fixPosition();
}

// Called every frame, if the behaviour is enabled.
Main.prototype.update = function() {

    if (!this.need_redraw) {
        return;
    }

    var color = 0xFF0000;
    if (this.lose_btn.value > this.gain_btn.value)
        color = 0x00FF00;
    this.graphics.clear();
    this.graphics.moveTo(this.success_btn.x, this.success_btn.y);
    this.graphics.beginFill(color);
    this.graphics.lineStyle(2, 0xffd900, 1);
    this.graphics.lineTo(this.lose_btn.x, this.lose_btn.y);
    this.graphics.lineTo(this.gain_btn.x, this.gain_btn.y);
    this.graphics.lineTo(this.success_btn.x, this.success_btn.y);
    this.graphics.endFill();

    this.need_redraw = false;
};


}).call(this, this, Object);
